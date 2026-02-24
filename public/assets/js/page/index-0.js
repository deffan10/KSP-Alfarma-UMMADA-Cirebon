"use strict";


// Initialize Chart only if canvas exists and getContext() is valid
var statisticsCanvas = document.getElementById("myChart");
if (statisticsCanvas && statisticsCanvas.getContext) {
  var statistics_chart = statisticsCanvas.getContext('2d');

  var myChart = new Chart(statistics_chart, {
  type: 'line',
  data: {
    labels: typeof month !== 'undefined' ? month : [],
    datasets: [{
      label: 'Total Simpanan (Rp)',
      data: typeof total !== 'undefined' ? total : [],
      borderWidth: 2,
      borderColor: '#6777ef',
      backgroundColor: 'rgba(103, 119, 239, 0.1)',
      fill: true,
      pointBackgroundColor: '#6777ef',
      pointBorderColor: '#fff',
      pointRadius: 4,
      pointHoverRadius: 5
    }]
  },
  options: {
    legend: {
      display: true,
      position: 'top'
    },
    scales: {
      yAxes: [{
        scaleLabel: {
          display: true,
          labelString: 'Total Simpanan (Rp)'
        },
        gridLines: {
          display: true,
          drawBorder: false
        },
        ticks: {
          stepSize: 500000,
          callback: function(value) {
            return value >= 1000000 ? (value / 1000000) + 'jt' : value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
          }
        }
      }],
      xAxes: [{
        scaleLabel: {
          display: true,
          labelString: 'Bulan'
        },
        gridLines: {
          color: '#fbfbfb',
          lineWidth: 2
        }
      }]
    }
  }
  }); 
}


if ($('#visitorMap').length && $('#visitorMap').width() > 0 && $('#visitorMap').height() > 0) {
  $('#visitorMap').vectorMap(
  {
  map: 'world_en',
  backgroundColor: '#ffffff',
  borderColor: '#f2f2f2',
  borderOpacity: .8,
  borderWidth: 1,
  hoverColor: '#000',
  hoverOpacity: .8,
  color: '#ddd',
  normalizeFunction: 'linear',
  selectedRegions: false,
  showTooltip: true,
  pins: {
    id: '<div class="jqvmap-circle"></div>',
    my: '<div class="jqvmap-circle"></div>',
    th: '<div class="jqvmap-circle"></div>',
    sy: '<div class="jqvmap-circle"></div>',
    eg: '<div class="jqvmap-circle"></div>',
    ae: '<div class="jqvmap-circle"></div>',
    nz: '<div class="jqvmap-circle"></div>',
    tl: '<div class="jqvmap-circle"></div>',
    ng: '<div class="jqvmap-circle"></div>',
    si: '<div class="jqvmap-circle"></div>',
    pa: '<div class="jqvmap-circle"></div>',
    au: '<div class="jqvmap-circle"></div>',
    ca: '<div class="jqvmap-circle"></div>',
    tr: '<div class="jqvmap-circle"></div>',
  },
  });
}

// weather
// Weather: call only if simpleWeather plugin is available
if ($.isFunction($.simpleWeather)) {
  getWeather();
  setInterval(getWeather, 600000);

  function getWeather() {
    $.simpleWeather({
      location: 'Bogor, Indonesia',
      unit: 'c',
      success: function(weather) {
        var html = '';
        html += '<div class="weather">';
        html += '<div class="weather-icon text-primary"><span class="wi wi-yahoo-' + weather.code + '"></span></div>';
        html += '<div class="weather-desc">';
        html += '<h4>' + weather.temp + '&deg;' + weather.units.temp + '</h4>';
        html += '<div class="weather-text">' + weather.currently + '</div>';
        html += '<ul><li>' + weather.city + ', ' + weather.region + '</li>';
        html += '<li> <i class="wi wi-strong-wind"></i> ' + weather.wind.speed+' '+weather.units.speed + '</li></ul>';
        html += '</div>';
        html += '</div>';

        $("#myWeather").html(html);
      },
      error: function(error) {
        $("#myWeather").html('<div class="alert alert-danger">'+error+'</div>');
      }
    });
  }
}
