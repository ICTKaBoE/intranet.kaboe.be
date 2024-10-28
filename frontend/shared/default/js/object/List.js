import Helpers from "./Helpers.js";

export default class List {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this.extraData = this.element.dataset.extra || false;
		this.template = this.element.dataset.template || false;

		if (this.extraData) {
			let extraData = this.extraData
				.replace("[", "")
				.replace("]", "")
				.split("|");

			this.extraData = {};
			extraData.forEach((v) => {
				v = v.split("=");
				this.extraData[v[0]] = v[1];
			});
		}

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='list']").each((ids, el) => {
			if (!List.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				List.INSTANCES[el.getAttribute("id")] = new List(el);
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("lst")) id = `lst${id}`;
		return Table.INSTANCES[id] || false;
	};

	init = async () => {
		await this.getData();
		this.fill();
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done((data) => {
			this.data = data;
		});
	};

	fill = () => {
		this.data.items.forEach((i) => {
			i = Helpers.flattenObject(i);
			let item = this.template;

			Object.keys(i).forEach((k) => {
				item = item.replace(`@${k}@`, i[k]);
			});

			this.element.innerHTML += item;
		});
	};
}
