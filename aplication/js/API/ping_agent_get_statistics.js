function get_host_statistics(time_range) {
    if (!hostId || isNaN(hostId)) {
        ShowAlert('error', 'Error', 'Invalid id', 'error');
        return;
    }

    fetch(`../php/API/ping_agent_get_statistics.php?id=${hostId}&time_range=${time_range}`)
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                throw new Error(`[Response error]: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
            } else {
                create_get_host_statistics(data)
                if (data.latency_time_json) {
                    create_latency_graph(data.latency_time_json);
                }
                //create_latency_graph(data.latency_time_json)
            }
        })
        .catch(error => {
            console.error('Error fetching host statistics:', error);
            ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error');
        });
}

function create_get_host_statistics(data) {
    const latency_average = document.getElementById('latency_average');
    const latency_minimum = document.getElementById('latency_minimum');
    const latency_maximum = document.getElementById('latency_maximum');
    const availability_percentage = document.getElementById('availability_percentage');

    if (data.average_latency != null && data.minimum_latency != null && data.maximum_latency != null) {
        latency_average.textContent = parseFloat(data.average_latency.toFixed(2)) + 'ms';
        latency_minimum.textContent = data.minimum_latency + 'ms';
        latency_maximum.textContent = data.maximum_latency + 'ms';
    } else {
        latency_average.innerHTML = latency_minimum.innerHTML = latency_maximum.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="fill: ${getComputedStyle(document.documentElement).getPropertyValue('--text-color').trim()} !important;" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                                                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                                                                                            </svg>`;
    }

    availability_percentage.innerHTML = `
                <span class="tooltip">
                        <div class="circle-percent box" style="background: conic-gradient(${color_select(data.uptime_percentage)} 0% ${data.uptime_percentage}%, transparent ${data.uptime_percentage}% 100%);">
                            <div class="circle-percent core">
                                <p id="uptime_percentage">${data.uptime_percentage}%</p>
                            </div>
                        </div>
                    <span class="tooltip-text">Availability is calculated considering all records for the period, not their averages.</span>
                </span>
    `;
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
            },
            ...(globalVariable_threshold != null && !isNaN(globalVariable_threshold) && {
                markLine: {
                    silent: true,
                    lineStyle: {
                        color: 'yellow',
                        type: 'dashed'
                    },
                    label: {
                        formatter: `{c}ms`, // Agrega el texto personalizado
                        position: 'end', // Posición de la etiqueta
                        color: 'yellow', // Color del texto
                    },
                    data: [{ yAxis: globalVariable_threshold }]
                }
            })
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

function color_select(rang_color) {

    //rang_color = parseInt(rang_color);

    if (rang_color > 80) {
        return getComputedStyle(document.documentElement).getPropertyValue('--accent-green').trim();
    } else if (rang_color > 40 && rang_color <= 80) {
        return getComputedStyle(document.documentElement).getPropertyValue('--accent-yellow').trim();
    } else {
        return getComputedStyle(document.documentElement).getPropertyValue('--accent-red').trim();
    }
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