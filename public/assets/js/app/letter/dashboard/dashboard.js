$(document).ready(function () {
  setChart("#north-america-chart", 'north-america-legend', 134, '#10312b');

});

function setChart(idChart, idFinalChart, value, color) {
  // segunda funciorn 
  if ($(idChart).length) {
    let areaData = {
      labels: ["Mis turnos"],
      datasets: [{
        data: [100],
        backgroundColor: [
          color,
        ],
        borderColor: "rgba(0,0,0,0)"
      }
      ]
    };
    let areaOptions = {
      responsive: true,
      maintainAspectRatio: true,
      segmentShowStroke: false,
      cutoutPercentage: 78,
      elements: {
        arc: {
          borderWidth: 4
        }
      },
      legend: {
        display: false
      },
      tooltips: {
        enabled: true
      },
      legendCallback: function (chart) {
        let text = [];
        text.push('<div class="report-chart">');
        text.push('<div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: ' + chart.data.datasets[0].backgroundColor[0] + '"></div><p class="mb-0">Mis turnos capturados</p></div>');
        text.push('<p class="mb-0"> ' + value + '</p>');
        text.push('</div>');
        text.push('</div>');
        return text.join("");
      },
    }
    let northAmericaChartPlugins = {
      beforeDraw: function (chart) {
        let width = chart.chart.width,
          height = chart.chart.height,
          ctx = chart.chart.ctx;

        ctx.restore();
        let fontSize = 3.125;
        ctx.font = "500 " + fontSize + "em sans-serif";
        ctx.textBaseline = "middle";
        ctx.fillStyle = "#13381B";

        let text = value,
          textX = Math.round((width - ctx.measureText(text).width) / 2),
          textY = height / 2;

        ctx.fillText(text, textX, textY);
        ctx.save();
      }
    }
    let northAmericaChartCanvas = $(idChart).get(0).getContext("2d");
    let northAmericaChart = new Chart(northAmericaChartCanvas, {
      type: 'doughnut',
      data: areaData,
      options: areaOptions,
      plugins: northAmericaChartPlugins
    });
    document.getElementById(idFinalChart).innerHTML = northAmericaChart.generateLegend();
  }
}