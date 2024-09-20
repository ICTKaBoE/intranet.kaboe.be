import Helpers from "./Helpers.js";

export default class Legenda {
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
				.split(";");

			this.extraData = {};
			extraData.forEach((v) => {
				v = v.split("=");
				this.extraData[v[0]] = v[1];
			});
		}

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='legenda']").each((ids, el) => {
			if (!Legenda.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Legenda.INSTANCES[el.getAttribute("id")] = new Legenda(el);
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("tbl")) id = `tbl${id}`;
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
