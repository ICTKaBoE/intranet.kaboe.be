import Helpers from "./Helpers.js";

export default class Chart {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;
		this.type = this.element.dataset.type || "line";
		this.source = this.element.dataset.source || false;
		this.title = this.element.dataset.title || false;
		this.group = this.element.dataset.group || false;
		this.legendPosition = this.element.dataset.legendPosition || "bottom";
		this.noDataText = this.element.dataset.noDataText || "Loading...";
		this.formatter = this.element.dataset.formatter || false;

		this.data = {};
		this.extraData = {};

		this.options = {};

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='chart']").each((ids, el) => {
			if (!Chart.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Chart.INSTANCES[el.getAttribute("id")] = new Chart(el);
		});
	}

	static GetInstance = (id) => {
		if (!id.startsWith("crt")) id = `crt${id}`;
		return Chart.INSTANCES[id] || false;
	};

	static ReloadAll = () => {
		for (const crt in Chart.INSTANCES) {
			Chart.INSTANCES[crt].reload();
		}
	};

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
				height: "350vh",
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
				type: "category",
			},
			yaxis: {
				labels: {
					padding: 4,
				},
			},
			legend: {
				show: true,
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
				if (this.formatter instanceof Function)
					return this.formatter(v);
				else return window[this.formatter](v);
			};

			this.options.tooltip.y.formatter = (v) => {
				if (this.formatter instanceof Function)
					return this.formatter(v);
				else return window[this.formatter](v);
			};
		}

		// if (this.data.labels) this.options.labels = this.data.labels;
		// if (this.data.series) this.options.series = this.data.series;
		// if (this.data.colors) this.options.colors = this.data.colors;
		// if (this.data.yaxis) this.options.yaxis = this.data.yaxis;
		// if (this.data.xaxis?.categories)
		// 	this.options.xaxis.categories = this.data.xaxis.categories;
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
