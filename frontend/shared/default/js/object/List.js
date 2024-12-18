import Helpers from "./Helpers.js";

export default class List {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this.extraDataString = this.element.dataset.extra || false;
		this.template = this.element.dataset.template || false;
		this.limit = this.element.dataset.limit || 200;

		this.element.removeAttribute("data-template");

		this.extraData = {};

		if (this.extraDataString) {
			let extraData = this.extraDataString
				.replace("[", "")
				.replace("]", "")
				.split("|");

			extraData.forEach((v) => {
				v = v.split("=");
				this.extraData[v[0]] = v[1];
			});
		}

		this.extraData.template = this.template;
		this.extraData.limit = this.limit;
		this.extraData.page = 0;

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
		return List.INSTANCES[id] || false;
	};

	static SearchAll = (value) => {
		for (const lst in List.INSTANCES) {
			List.INSTANCES[lst].search(value);
		}
	};

	init = async () => {
		await this.getData();
		this.fill();
		if (!this.stopCheckNext) this.checkNext();
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done((data) => {
			this.data = data;
		});
	};

	fill = () => {
		let data = this.data.raw;
		if (this.data?._raw == "base64") data = atob(this.data.raw);

		if (this.extraData?.page == 0) this.element.innerHTML = data;
		else this.element.innerHTML += data;
	};

	search = async (value) => {
		this.stopCheckNext = value.length > 0;
		this.extraData.page = 0;
		this.extraData.search = value;

		await this.getData();
		this.fill();
		this.checkNext();
	};

	checkNext = () => {
		if (this.data.next) {
			setTimeout(async () => {
				this.extraData.page++;

				await this.getData();
				this.fill();
				if (!this.stopCheckNext) this.checkNext();
			}, 1000);
		}
	};
}
