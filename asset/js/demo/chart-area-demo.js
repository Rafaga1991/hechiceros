function initCharArea(id, data, type = 'line') {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    // Area Chart Example
    var ctx = document.getElementById(id);
    var myLineChart = new Chart(ctx, {
        type: type,
        data: {
            labels: data.label,
            datasets: data.datasets,
        },
        options: {
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: true
                    },
                    ticks: {
                        maxTicksLimit: data.label.length
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: data.max,
                        maxTicksLimit: 8
                    },
                    gridLines: {
                        color: "rgba(0, 0, 0, .125)",
                    }
                }],
            },
            legend: {
                display: true
            }
        }
    });
}