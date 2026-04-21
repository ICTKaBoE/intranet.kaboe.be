export default class MasterObject {
	static INSTANCES = [];
	static OBJ_SELECTOR = false;
	static OBJ_ID_PREFIX = false;

	static ScanAndCreate() {
		if (!this.INSTANCES.hasOwnProperty(this.name))
			this.INSTANCES[this.name] = [];

		$(this.OBJ_SELECTOR).each((ids, el) => {
			if (
				!this.INSTANCES[this.name].hasOwnProperty(el.getAttribute("id"))
			)
				this.INSTANCES[this.name][el.getAttribute("id")] = new this(el);
		});
	}

	static GetInstance(id) {
		if (this.OBJ_ID_PREFIX && !id.startsWith(this.OBJ_ID_PREFIX))
			id = `${this.OBJ_ID_PREFIX}${
				String(id).charAt(0).toUpperCase() + String(id).slice(1)
			}`;
		return this.INSTANCES[this.name][id] || null;
	}

	static ReloadAll() {
		for (const i in this.INSTANCES[this.name]) {
			if (typeof this.INSTANCES[this.name][i].reload === "function")
				this.INSTANCES[this.name][i].reload();
		}
	}

	static Loaded() {
		return !Object.keys(this.INSTANCES[this.name])
			.map((i) => i.loaded)
			.includes(false);
	}

	static SearchAll(value) {
		for (const i in this.INSTANCES[this.name]) {
			if (typeof this.INSTANCES[this.name][i].search === "function")
				this.INSTANCES[this.name][i].search(value);
		}
	}
}
