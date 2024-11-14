// Inicializar el gráfico
var myChart = echarts.init(document.getElementById('latency_graph'));

// Cargar los datos inicialmente
let data = []; // Inicializar el array de datos


function createChartData(newData) {
  data = newData;
  const filteredData = filterDataByRange(selectedRange);

  option.xAxis.data = filteredData.map(item => item.time); // Guardamos solo la marca de tiempo (segundos)
  option.series[0].data = filteredData.map(item => item.value);

  // Ajustar rango de dataZoom en función del rango seleccionado
  if (selectedRange === '10m') {
    option.dataZoom[0].start = 90; // Mostrar últimos 10 minutos
    option.dataZoom[0].end = 100;
  } else {
    option.dataZoom[0].start = 0;
    option.dataZoom[0].end = 100;
  }

  // Actualizar el gráfico
  myChart.setOption(option, true);
}



// Función para filtrar los datos por rango de tiempo
function filterDataByRange(range) {
  const now = Date.now() / 1000; // Tiempo actual en segundos
  let filteredData = [];

  if (range === '10m') {
    filteredData = data.filter(item => item.time >= now - 600); // Últimos 10 minutos
  } else if (range === '24h') {
    filteredData = data.filter(item => item.time >= now - 86400); // Últimas 24 horas
  } else if (range === '1m') {
    filteredData = data.filter(item => item.time >= now - 2592000); // Último mes (30 días)
  } else if (range === '1y') {
    filteredData = data.filter(item => item.time >= now - 31536000); // Último año (365 días)
  }

  return filteredData;
}

// Llamar a la función cada 30 segundos
//setInterval(updateChartData, 30000);

var option = {
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
    data: [], // Actualizado en createChartData
    axisLabel: {
      interval: function (index, value) {
        // Muestra una etiqueta cada hora (3600 segundos)
        const currentDate = new Date(value * 1000);
        return currentDate.getMinutes() === 0; // Solo mostrar etiquetas en la hora exacta
      },
      formatter: function (value) {
        // Formato: día, mes, hora
        const date = new Date(value * 1000);
        return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' }) + ' ' +
               date.toLocaleTimeString('es-ES', { hour: '2-digit' });
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
    data: [],
    type: 'line',
    smooth: true,
    lineStyle: {
      color: '#b499ff'
    },
    showSymbol: false,
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
      const formattedDate = date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' }) + ' ' +
                            date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
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




// Inicializar el gráfico con las opciones
myChart.setOption(option);
window.addEventListener('resize', myChart.resize); // Adaptar al tamaño del contenedor

