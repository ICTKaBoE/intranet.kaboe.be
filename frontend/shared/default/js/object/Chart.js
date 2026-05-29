import MasterObject from "../MasterObject.js";

export default class Chart extends MasterObject {
  static OBJ_SELECTOR = "div[role='chart']";
  static OBJ_ID_PREFIX = "crt";

  constructor(element) {
    super();

    this.element = element;
    this.id = this.element.id || false;
    this.type = this.element.dataset.type || "line";
    this.source = this.element.dataset.source || false;
    this.title = this.element.dataset.title || false;
    this.group = this.element.dataset.group || false;
    this.legend = this.element.hasAttribute("data-legend") || false;
    this.legendPosition = this.element.dataset.legendPosition || "bottom";
    this.noDataText = this.element.dataset.noDataText || "Loading...";
    this.formatter = this.element.dataset.formatter || false;
    this.xaxisType = this.element.dataset.xaxisType || "category";
    this.yaxisMin = this.element.dataset.yaxisMin || 0;
    this.yaxisMax = this.element.dataset.yaxisMax || undefined;
    this.height = this.element.dataset.height || "350vh";
    this.horizontal = this.element.hasAttribute("data-horizontal") || false;

    this.data = {};
    this.extraData = {};

    this.options = {};

    this.init();
  }

  init = async () => {
    this.createOptions();
    this.createChart();
    await this.getData();
    this.updateChart();
  };

  reload = async () => {
    await this.getData();
    this.updateChart();
  };

  createOptions = () => {
    this.options = {
      chart: {
        id: this.id,
        type: this.type,
        fontFamily: "inherit",
        height: this.height,
        parentHeightOffset: 0,
        toolbar: {
          show: false,
        },
        animations: {
          enabled: false,
        },
      },
      stroke: {
        width: 2,
        lineCap: "round",
        curve: "straight",
      },
      tooltip: {
        theme: "dark",
        shared: true,
        intersect: false,
        y: {},
      },
      grid: {
        padding: {
          top: -20,
          right: 0,
          left: -4,
          bottom: -4,
        },
        strokeDashArray: 4,
      },
      xaxis: {
        labels: {
          padding: 0,
        },
        tooltip: {
          enabled: false,
        },
        axisBorder: {
          show: false,
        },
        type: this.xaxisType,
      },
      yaxis: {
        min: this.yaxisMin,
        max: this.yaxisMax,
        labels: {
          padding: 4,
        },
      },
      legend: {
        show: this.legend,
        position: this.legendPosition,
        horizontalAlign: "left",
        offsetX: 40,
        markers: {
          width: 10,
          height: 10,
          radius: 100,
        },
        itemMargin: {
          horizontal: 8,
          vertical: 8,
        },
      },
      fill: {},
      series: [],
      annotations: {
        position: "back",
        yaxis: [],
      },
      noData: {
        text: this.noDataText,
      },
    };

    if (this.title)
      this.options.title = {
        text: this.title,
        align: "left",
      };

    if (this.group) this.options.chart.group = this.group;
    if (this.formatter) {
      this.options.yaxis.labels.formatter = (v) => {
        if (this.formatter instanceof Function) return this.formatter(v);
        else return window[this.formatter](v);
      };

      this.options.tooltip.y.formatter = (v) => {
        if (this.formatter instanceof Function) return this.formatter(v);
        else return window[this.formatter](v);
      };
    }

    if (this.type == "donut" || this.type == "pie") {
      this.options.plotOptions = {
        pie: {
          donut: {
            labels: {
              show: true,
              total: {
                show: true,
              },
            },
          },
        },
      };
    } else if (this.type == "bar") {
      this.options.plotOptions = {
        bar: {
          horizontal: this.horizontal,
        },
      };
    }
  };

  createChart = () => {
    this.apexChart = new ApexCharts(this.element, this.options);
    this.apexChart.render();
  };

  getData = () => {
    if (!this.source) return;

    return $.get(this.source, this.extraData).done((data) => {
      this.data = data;
    });
  };

  updateChart = () => {
    if (this.data.options) this.apexChart.updateOptions(this.data.options);
    if (this.data.series) this.apexChart.updateSeries(this.data.series);
  };

  addExtraData = (key, value) => {
    this.extraData[key] = value;
  };

  removeExtraData = (key) => {
    delete this.extraData[key];
  };

  clearExtraData = () => {
    this.extraData = {};
  };

  destroy = () => {
    if (this.apexChart) this.apexChart.destroy();
  };
}
