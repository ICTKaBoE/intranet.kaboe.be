import Helpers from "./Helpers.js";

export default class Chart {
	static INSTANCES = {};

	constructor(element) {
		this.chart = element;
		this.id = this.chart.id || false;
		this.type = this.chart.dataset.type || 'line';
		this.source = this.chart.dataset.source || false;

		this.data = {};
		this.extraData = {};

		this.options = {};

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='chart']").each((ids, el) => {
			Chart.INSTANCES[el.id] = new Chart(el);
		});
	}

	init = async () => {
		await this.getData();
		this.createOptions();
		this.createChart();
	};

	createOptions = () => {
		if (!this.data.series) return;

		this.options.chart = {};
		this.options.chart.type = this.type;

		this.options.chart.animations = {};
		this.options.chart.animations.enabled = false;

		this.options.chart.toolbar = {};
		this.options.chart.toolbar.show = false;

		this.options.tooltip = {};
		this.options.tooltip.theme = "dark";

		if (this.data.labels) this.options.labels = this.data.labels;
		if (this.data.series) this.options.series = this.data.series;
		if (this.data.colors) this.options.colors = this.data.colors;

		this.options.plotOptions = {};

		if (this.type === "bar") {
			this.options.plotOptions.bar = {};
			this.options.plotOptions.bar.horizontal = true;

			this.options.dataLabels = {};
			this.options.dataLabels.enabled = false;

			this.options.tooltip.shared = true;
			this.options.tooltip.intersect = false;

			this.options.xaxis = {};
			this.options.xaxis.type = "category";

			this.options.xaxis.tooltip = {};
			this.options.xaxis.tooltip.enabled = false;

			this.options.xaxis.axisBorder = {};
			this.options.xaxis.axisBorder.show = false;

			if (this.data.xaxis.categories) this.options.xaxis.categories = this.data.xaxis.categories;
		}
	};

	createChart = () => {
		if (!this.data.series) return;

		this.apexChart = new ApexCharts(this.chart, this.options);
		this.apexChart.render();
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done(data => {
			this.data = data;
		});
	};
}