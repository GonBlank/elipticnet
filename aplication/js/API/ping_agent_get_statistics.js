function get_host_statistics(time_range) {
    if (!hostId || isNaN(hostId)) {
        ShowAlert('error', 'Error', 'Invalid host ID', 'error');
        return;
    }

    fetch(`../php/API/ping_agent_get_statistics.php?id=${hostId}&time_range=${time_range}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type);
            } else {
                create_get_host_statistics(data)
                create_latency_graph(data.latency_time_json)
            }
        })
        .catch(error => {
            console.error('Error fetching host statistics:', error);
            ShowAlert('error', 'Error', `Failed to load host data: ${error.message || error}`, 'error');
        });
}

function create_get_host_statistics(data) {
    const latency_average = document.getElementById('latency_average');
    const latency_minimum = document.getElementById('latency_minimum');
    const latency_maximum = document.getElementById('latency_maximum');

    if (data.average_latency != null && data.minimum_latency != null && data.maximum_latency != null) {
        latency_average.textContent = parseFloat(data.average_latency.toFixed(2)) + 'ms';
        latency_minimum.textContent = data.minimum_latency + 'ms';
        latency_maximum.textContent = data.maximum_latency + 'ms';
    }
}

function create_latency_graph(latencyDataJson) {
    // Parsear el JSON recibido
    const latencyData = JSON.parse(latencyDataJson);

    // Extraer valores para el gráfico
    const times = latencyData.map(entry => new Date(entry.time + ' UTC')); // Convertir la fecha GMT a Date en la zona horaria local
    const latencies = latencyData.map(entry => entry.latency); // Eje Y (latencias)

    // Establecer formato de etiquetas basado en el rango
    let timeFormatter;

    if (time_range === 1) {
        //("Mon 23:58")
        timeFormatter = date => `${date.toLocaleDateString('en-US', { weekday: 'short' })} ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false })}`;
    } else if (time_range === 2) {
        //("Mon 5", "Tue 6")
        timeFormatter = date => `${date.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' })}`;
    } else if (time_range === 3) {
        //("Nov 2024")
        timeFormatter = date => `${date.toLocaleDateString('en-US', { month: 'short' })} ${date.getFullYear()}`;
    } else {
        // Último año: Nombre del mes (ejemplo: "JUN", "JUL")
        //timeFormatter = date => date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        //("Mon 23:58")
        timeFormatter = date => `${date.toLocaleDateString('en-US', { weekday: 'short' })} ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false })}`;
    }

    // Formatear etiquetas del eje X
    const formattedTimes = times.map(time => timeFormatter(new Date(time)));

    //Escala dinamica
    let time_scale = dynamic_scale(formattedTimes.length);


    // Configuración del gráfico
    const option = {
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const { axisValue, data } = params[0];
                const latencyText = data === null ? "Host down" : `${data} ms`;
                return `Time: ${axisValue}<br>Latency: ${latencyText}`;
            },
            backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphTooltipBg-color').trim(),
            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphTooltip-border-color').trim(),
            borderWidth: 1,
            padding: [10, 15],
            textStyle: {
                color: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphTooltip-text-color').trim()
            }
        },
        xAxis: {
            type: 'category',
            data: formattedTimes,
            name: 'Time',
            axisLabel: {
                rotate: 45,
                interval: time_scale,
                textStyle: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--color-text').trim()
                }
            },
            axisLine: {
                lineStyle: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--color-line').trim()
                }
            },
            splitLine: {
                show: false
            }
        },
        yAxis: {
            type: 'log',
            logBase: 10,
            name: 'Latency [ms]',
            axisLabel: {
                textStyle: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color').trim()
                }
            },
            axisLine: {
                lineStyle: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-color').trim()
                }
            },
            splitLine: {
                lineStyle: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphGrid-color').trim()
                }
            }
        },
        series: [{
            data: latencies,
            type: 'line',
            smooth: true,
            areaStyle: {
                color: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphArea-color').trim()
            },
            lineStyle: {
                color: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphLine-color').trim(),
                width: 2,
                type: 'solid'
            },
            symbol: 'circle',
            symbolSize: 6,
            itemStyle: {
                color: getComputedStyle(document.documentElement).getPropertyValue('--LatencyGraphDot-color').trim()
            }
        }],
        // Habilitar zoom sin la barra
        dataZoom: [
            {
                type: 'inside', // Habilitar solo zoom con rueda del mouse
                xAxisIndex: 0 // Aplica el zoom al eje X
            }
        ]
    };
    
    








    // Inicializar el gráfico
    const myChart = echarts.init(document.getElementById('latency_graph'));
    myChart.setOption(option);

    // Ajustar tamaño dinámico al redimensionar ventana
    window.addEventListener('resize', myChart.resize);
}

function dynamic_scale(data_length) {
    // Definir los límites máximos por time_range
    const time_limits = {
        1: 60,    // Último día
        2: 1440,  // Último mes
        3: 17280  // Último año
    };

    // Determinar el límite máximo según time_range
    const max_limit = time_limits[time_range] || Math.floor(data_length / 15);

    // Calcular time_scale
    let time_scale;
    if (data_length <= 15) {
        time_scale = 0;
    } else if (data_length >= max_limit) {
        time_scale = max_limit;
    } else {
        time_scale = Math.floor(data_length / 15);
    }

    return time_scale;
}


let time_range = 1; // 1:1 DAY / 2:1 MONTH / 3:1 YEAR

function updateSelectedRange(selectedValue) {
    time_range = parseInt(selectedValue); // Actualiza el valor de time_range
    get_host_statistics(time_range); // Llama a la función con el nuevo rango de tiempo
}

get_host_statistics(time_range);

setInterval(() => {
    get_host_statistics(time_range);

}, 60000); // 60000 ms = 1 minuto
