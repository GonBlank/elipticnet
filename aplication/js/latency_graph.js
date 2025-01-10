function createChartDataLast24Hs(latencyData) {
    const timestamps = [];
    const values = [];

    latencyData.forEach(dataPoint => {
        timestamps.push(dataPoint.time);
        // Si el valor es null, lo reemplazamos por 0
        values.push(dataPoint.value !== null ? dataPoint.value : 0); 
    });

    const option = {
        xAxis: {
            type: 'category',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            boundaryGap: false,
            data: timestamps,
            axisLabel: {
                interval: function (index, value) {
                    if (timestamps.length === 1) return true;
                    const totalDataPoints = timestamps.length;
                    const maxLabels = 10;
                    const step = Math.floor(totalDataPoints / maxLabels);
                    return index % step === 0;
                },
                formatter: function (value) {
                    const date = new Date(value * 1000);
                    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                },
                rotate: 45
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#130c27'
                }
            },
            axisLabel: {
                formatter: '{value} ms'
            }
        },
        series: [{
            data: values,
            type: 'line',
            smooth: true,
            lineStyle: {
                color: '#b499ff'
            },
            showSymbol: true,
            symbolSize: 2,
            itemStyle: {
                // Aquí se asigna color rojo solo para los valores que fueron originalmente null
                color: function(params) {
                    // Si el valor es 0, pero fue originalmente null, se le asigna color rojo
                    return latencyData[params.dataIndex].value === null ? '#FF0000' : '#5e3dc2';
                }
            },
            animation: true,
            animationDuration: 1000,
            animationEasing: 'cubicInOut',
            connectNulls: false
        }],
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const date = new Date(params[0].name * 1000);
                const formattedDate = date.toLocaleDateString('es-ES', { weekday: 'short' }) + ' ' +
                                      date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                
                // Verificamos si el valor original es null
                const isNullValue = latencyData[params[0].dataIndex].value === null;
                
                return `${formattedDate} <br/> ${isNullValue ? 'No reply' : params[0].value + ' ms'}`;
            },
            backgroundColor: '#130c27',
            textStyle: {
                color: '#F5F0FE'
            }
        },
        dataZoom: [{
            type: 'inside',
            start: 0,
            end: 100
        }]
    };

    const myChart = echarts.init(document.getElementById('latency_graph'));
    myChart.setOption(option);
    window.addEventListener('resize', myChart.resize);
}

function createChartDataLastMonth(latencyData) {
    const currentTime = Date.now(); // Hora actual en milisegundos
    const oneMonthAgo = currentTime - 30 * 24 * 60 * 60 * 1000; // Hace 30 días en milisegundos (aproximadamente 1 mes)

    const timestamps = [];
    const values = [];

    // Filtrar los datos para el último mes y agruparlos por día
    const groupedData = {}; // Objeto para almacenar datos agrupados por día

    latencyData.forEach(dataPoint => {
        const timestamp = dataPoint.time * 1000; // Convertir tiempo a milisegundos

        // Filtrar los datos para el último mes
        if (dataPoint.value !== null && timestamp >= oneMonthAgo) {
            const date = new Date(timestamp);
            const day = new Date(date.setHours(0, 0, 0, 0)).getTime(); // Convertir la fecha a medianoche (solo el día)

            // Si no existe la fecha en el objeto, inicializamos con un array vacío
            if (!groupedData[day]) {
                groupedData[day] = [];
            }

            // Agregar el valor al grupo de ese día
            groupedData[day].push(dataPoint.value);
        }
    });

    // Ahora, extraemos los datos agrupados en los arrays que usaremos para el gráfico
    for (const day in groupedData) {
        if (groupedData.hasOwnProperty(day)) {
            const averageLatency = groupedData[day].reduce((sum, value) => sum + value, 0) / groupedData[day].length; // Promediar los valores del día
            timestamps.push(Number(day) / 1000); // Almacenar el timestamp del día (en segundos)
            values.push(parseFloat(averageLatency.toFixed(2))); // Redondear a 2 decimales
        }
    }

    // Crear las opciones para el gráfico
    const option = {
        xAxis: {
            type: 'category',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            boundaryGap: false,
            data: timestamps, // Usar los días agrupados
            axisLabel: {
                interval: 0, // Asegura que se muestre una etiqueta por cada día
                formatter: function (value) {
                    const date = new Date(value * 1000);
                    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' }); // Formato: mes y día
                }
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#130c27'
                }
            },
            axisLabel: {
                formatter: '{value} ms'
            }
        },
        series: [{
            data: values, // Valores de latencia promedio por día
            type: 'line',
            smooth: true,
            lineStyle: {
                color: '#b499ff'
            },
            showSymbol: true,
            symbolSize: 2, // Tamaño de los puntos
            itemStyle: {
                color: '#5e3dc2'
            },
            animation: true,
            animationDuration: 1000,
            animationEasing: 'cubicInOut'
        }],
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const date = new Date(params[0].name * 1000);
                const formattedDate = date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
                return `${formattedDate} <br/> ${params[0].value} ms`;
            },
            backgroundColor: '#130c27',
            textStyle: {
                color: '#F5F0FE'
            }
        },
        dataZoom: [{
            type: 'inside',
            start: 0,
            end: 100
        }]
    };

    const myChart = echarts.init(document.getElementById('latency_graph'));
    myChart.setOption(option);
    window.addEventListener('resize', myChart.resize);
}

function createChartDataLastYear(latencyData) {
    const currentTime = Date.now(); // Hora actual en milisegundos
    const oneYearAgo = currentTime - 365 * 24 * 60 * 60 * 1000; // Hace 1 año en milisegundos

    const timestamps = [];
    const values = [];

    // Filtrar los datos para el último año y agruparlos por mes
    const groupedData = {}; // Objeto para almacenar datos agrupados por mes

    latencyData.forEach(dataPoint => {
        const timestamp = dataPoint.time * 1000; // Convertir tiempo a milisegundos

        // Filtrar los datos para el último año
        if (dataPoint.value !== null && timestamp >= oneYearAgo) {
            const date = new Date(timestamp);
            const monthKey = `${date.getFullYear()}-${date.getMonth() + 1}`; // Formato: Año-Mes

            // Si no existe el mes en el objeto, inicializamos con un array vacío
            if (!groupedData[monthKey]) {
                groupedData[monthKey] = [];
            }

            // Agregar el valor al grupo de ese mes
            groupedData[monthKey].push(dataPoint.value);
        }
    });

    // Ahora, extraemos los datos agrupados en los arrays que usaremos para el gráfico
    for (const monthKey in groupedData) {
        if (groupedData.hasOwnProperty(monthKey)) {
            const averageLatency = groupedData[monthKey].reduce((sum, value) => sum + value, 0) / groupedData[monthKey].length; // Promediar los valores del mes
            const [year, month] = monthKey.split('-');
            const timestamp = new Date(year, month - 1).getTime(); // Obtener timestamp del primer día del mes
            timestamps.push(timestamp / 1000); // Almacenar el timestamp del mes (en segundos)
            values.push(parseFloat(averageLatency.toFixed(2))); // Redondear a 2 decimales
        }
    }

    // Crear las opciones para el gráfico
    const option = {
        xAxis: {
            type: 'category',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            boundaryGap: false,
            data: timestamps, // Usar los meses agrupados
            axisLabel: {
                interval: 0, // Asegura que se muestre una etiqueta por cada mes
                formatter: function (value) {
                    const date = new Date(value * 1000);
                    return `${date.toLocaleString('es-ES', { month: 'short' })} ${date.getFullYear()}`; // Formato: mes y año
                }
            }
        },
        yAxis: {
            type: 'value',
            axisLine: {
                lineStyle: {
                    color: '#F5F0FE'
                }
            },
            splitLine: {
                lineStyle: {
                    color: '#130c27'
                }
            },
            axisLabel: {
                formatter: '{value} ms'
            }
        },
        series: [{
            data: values, // Valores de latencia promedio por mes
            type: 'line',
            smooth: true,
            lineStyle: {
                color: '#b499ff'
            },
            showSymbol: true,
            symbolSize: 2, // Tamaño de los puntos
            itemStyle: {
                color: '#5e3dc2'
            },
            animation: true,
            animationDuration: 1000,
            animationEasing: 'cubicInOut'
        }],
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const date = new Date(params[0].name * 1000);
                const formattedDate = `${date.toLocaleString('es-ES', { month: 'short' })} ${date.getFullYear()}`;
                return `${formattedDate} <br/> ${params[0].value} ms`;
            },
            backgroundColor: '#130c27',
            textStyle: {
                color: '#F5F0FE'
            }
        },
        dataZoom: [{
            type: 'inside',
            start: 0,
            end: 100
        }]
    };

    const myChart = echarts.init(document.getElementById('latency_graph'));
    myChart.setOption(option);
    window.addEventListener('resize', myChart.resize);
}



