class Chart {
    static INSTANCES = {};

    constructor(element) {
        this.chart = element;
        this.id = this.chart.id || false;
        this.source = this.chart.dataset.source || false;

        this.init();
    }

    init = () => {
        this.createChart();
        this.loadChart();
    };

    createChart = () => {
        let options = {
            chart: {
                type: 'bar',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: false
                },
                zoom: {
                    enabled: false
                },
            },
            series: [],
            noData: {
                text: 'Laden...'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'steline'
            },
            fill: {
                type: 'solid',
            },
            markers: {
                size: 3,
                hover: {
                    size: 5
                }
            },
            tooltip: {
                intersect: true,
                shared: false
            },
            xaxis: {
                type: 'datetime',
            },
            yaxis: {
                title: {
                    text: 'Kilometers'
                }
            }
        };

        this.apexChart = new ApexCharts(this.chart, options);
        this.apexChart.render();
    };

    loadChart = () => {
        if (!this.source) return;

        $.get(this.source).done(data => {
            data = JSON.parse(data);

            if (data.series) this.apexChart.updateSeries([
                {
                    data: data.series
                }
            ]);
        }).fail(returnData => {
            alert("Er is een fout gebeurd bij het laden van de chart!");
        });
    };
}