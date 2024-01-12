import Helpers from "./Helpers.js";

export default class Chart {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;
		this.type = this.element.dataset.type || 'line';
		this.source = this.element.dataset.source || false;

		this.data = {};
		this.extraData = {};

		this.options = {};

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='chart']").each((ids, el) => {
			if (!Chart.INSTANCES.hasOwnProperty(el.getAttribute("id"))) Chart.INSTANCES[el.getAttribute("id")] = new Chart(el);
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

			this.options.tooltip.y = {};
			this.options.tooltip.y.formatter = (val) => {
				return 	Number.isInteger(val) ? parseInt(val).toFixed(0) : val;
			};

			if (this.data.xaxis.categories) this.options.xaxis.categories = this.data.xaxis.categories;
		}
	};

	createChart = () => {
		if (!this.data.series) return;

		this.apexChart = new ApexCharts(this.element, this.options);
		this.apexChart.render();
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done(data => {
			this.data = data;
		});
	};
}