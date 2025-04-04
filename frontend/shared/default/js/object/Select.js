import Helpers from "./Helpers.js";

export default class Select {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.render = {};
		this.render.item = this.element.dataset.renderItem || false;
		this.render.option = this.element.dataset.renderOption || false;
		this.onChange = this.element.dataset.onChange || false;
		this.loadSource = this.element.dataset.loadSource || false;
		this.loadValue = this.element.dataset.loadValue || "id";
		this.loadLabel = this.element.dataset.loadLabel || false;
		this.defaultDetails = this.element.dataset.defaultDetails || false;
		this.defaultValue = this.element.dataset.defaultValue || false;
		this.defaultExtraData = this.element.dataset.extra || false;
		this.multiple = this.element.hasAttribute("multiple");
		this.search = this.element.hasAttribute("data-search");
		this.parent = this.element.dataset.parentSelect || false;
		this.defaultDisabled = this.element.hasAttribute("disabled") || false;
		this.optgroupAttribute =
			this.element.dataset.optgroupAttribute || false;
		this.optgroupValue = this.element.dataset.optgroupValue || false;
		this.optgroupLabel = this.element.dataset.optgroupLabel || false;
		this.limit = this.element.dataset.limit || 200;
		this.defaultNoLoad = this.element.hasAttribute("data-default-no-load");

		this.eventListeners = [];
		this.selectedDetails = false;
		this.data = {
			items: [],
		};
		this.loadParams = {};
		this.loadParams.limit = this.limit;
		this.loadParams.page = 0;

		this.stopCheckNext = false;

		if (this.loadSource && this.loadSource.startsWith("[")) {
			let loadSource = this.loadSource
				.replace("[", "")
				.replace("]", "")
				.split(";");

			this.loadSource = [];
			loadSource.forEach((source) => {
				source = source.split("@");
				this.loadSource[source[0]] = source[1];
			});

			if (!this.defaultDetails)
				this.defaultDetails = Object.keys(this.loadSource)[0];
		}

		if (this.loadValue && this.loadValue.startsWith("[")) {
			let loadValue = this.loadValue
				.replace("[", "")
				.replace("]", "")
				.split(";");

			this.loadValue = [];
			loadValue.forEach((source) => {
				source = source.split("@");
				this.loadValue[source[0]] = source[1];
			});
		}

		if (this.loadLabel && this.loadLabel.startsWith("[")) {
			let loadLabel = this.loadLabel
				.replace("[", "")
				.replace("]", "")
				.split(";");

			this.loadLabel = [];
			loadLabel.forEach((source) => {
				source = source.split("@");
				this.loadLabel[source[0]] = source[1];
			});
		}

		if (this.defaultExtraData) {
			let extraData = this.defaultExtraData
				.replace("[", "")
				.replace("]", "")
				.split("|");

			this.extraData = {};
			extraData.forEach((v) => {
				v = v.split("=");
				this.loadParams[v[0]] = v[1];
			});
		}

		this.loaded = false;
		this.init();
	}

	static ScanAndCreate() {
		$("select").each((ids, el) => {
			if (!Select.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Select.INSTANCES[el.getAttribute("id")] = new Select(el);
		});
	}

	static GetInstance = (id) => {
		return Select.INSTANCES[id] || false;
	};

	static Loaded() {
		let allLoaded = true;

		Object.keys(Select.INSTANCES).forEach((key) => {
			if (!Select.INSTANCES[key].loaded) allLoaded = false;
		});

		return allLoaded;
	}

	static ReloadAll = () => {
		for (const sel in Select.INSTANCES) {
			Select.INSTANCES[sel].clear();
		}
	};

	init = async () => {
		this.element.setAttribute("role", "select");
		this.element.removeAttribute("disabled");
		if (!this.element.classList.contains("form-select"))
			this.element.classList.add("form-select");

		if (!this.defaultNoLoad && this.loadSource) {
			this.loadParams.page = 0;
			await this.getData();
			if (!this.stopCheckNext) await this.checkNext();
		}

		this.createSelect();
		this.disable();
		if (this.parent) this.detectParentAndSetFunctions();
		this.setDefaultValue();
		if (!this.defaultDisabled) this.enable();
		this.loaded = true;
	};

	reload = async () => {
		this.loaded = false;
		this.disable();
		this.clear();
		this.destroy();

		if (this.loadSource) {
			this.loadParams.page = 0;
			await this.getData();
			if (!this.stopCheckNext) await this.checkNext();
		}

		this.createSelect();
		this.setEventListeners();
		this.setDefaultValue();
		if (!this.defaultDisabled) this.enable();
		this.loaded = true;
	};

	setDetails = (id) => {
		this.selectedDetails = id;
		this.data.items = [];
		this.reload();
	};

	createSelect = () => {
		let settings = {
			plugins: this.multiple ? ["remove_button", "checkbox_options"] : [],
			hideSelected: false,
			duplicates: true,
			maxOptions: null,
			maxItems: this.multiple ? null : 1,
			delimiter: this.multiple ? ";" : null,
			copyClassesToDropdown: false,
			dropdownClass: "dropdown-menu ts-dropdown",
			optionClass: "dropdown-item",
			persist: false,
			create: false,
			render: {},
			controlInput: null,
			searchField: ["text"],
		};

		if (this.render.item)
			settings.render.item = (data, escape) => {
				return window[this.render.item](data, escape);
			};

		if (this.render.option)
			settings.render.option = (data, escape) => {
				return window[this.render.option](data, escape);
			};

		if (this.onChange)
			settings.onChange = (value) => {
				window[this.onChange](value);
			};

		if (this.search) settings.controlInput = "<input>";

		if (this.optgroupAttribute) {
			settings.optgroupField = this.optgroupAttribute;
			settings.optgroupValueField = this.optgroupValue;
			settings.optgroupLabelField = this.optgroupLabel;
		}

		if (this.loadSource && this.loadValue && this.loadLabel) {
			settings.valueField =
				this.loadValue[this.selectedDetails || this.defaultDetails] ||
				this.loadValue;
			settings.labelField =
				this.loadLabel[this.selectedDetails || this.defaultDetails] ||
				this.loadLabel;
			settings.searchField = [
				this.loadLabel[this.selectedDetails || this.defaultDetails] ||
					this.loadLabel,
			];
		}

		if (this.data?.optgroups && this.optgroupAttribute)
			settings.optgroups = this.data.optgroups;

		if (this.data?.items && this.data?.items.length)
			settings.options = this.data.items;

		this.tomSelect = new TomSelect(this.element, settings);
	};

	getData = () => {
		if (!this.loadSource) return;

		return $.get(
			this.loadSource[this.selectedDetails || this.defaultDetails] ||
				this.loadSource,
			this.loadParams
		).done((data) => {
			let items = data.items;
			delete data.items;
			this.data = Object.assign(this.data, data);

			if (this.data.items.length == 0) this.data.items = items;
			else this.data.items.push(...items);
		});
	};

	checkNext = async () => {
		if (this.data.next) {
			await Helpers.sleep(100);
			this.loadParams.page++;

			await this.getData();
			if (!this.stopCheckNext) await this.checkNext();
		}
	};

	setDefaultValue = () => {
		if (this.defaultValue) this.setValue(this.defaultValue, false);
	};

	setValue = (value, silent = false) => {
		this.defaultValue = value;
		Helpers.CheckAllLoaded(() => {
			value = String(value)
				.split(";")
				.map((v) => (isNaN(v) ? v : parseFloat(v)));
			this.tomSelect.setValue(value, silent);
		});
	};

	getValue = () => {
		let items = this.tomSelect.getValue();
		if (Array.isArray(items)) items = items.join(";");
		return items;
	};

	getItemDetails = () => {
		let value = this.getValue();
		let details = null;

		$.each(this.tomSelect.options, (idx, opt) => {
			if (
				opt[
					this.loadValue[
						this.selectedDetails || this.defaultDetails
					] || this.loadValue
				] == value
			) {
				details = opt;
			}
		});

		return details;
	};

	setEventListeners = () => {
		Object.keys(this.eventListeners).forEach((key) => {
			this.setEventListener(key, this.eventListeners[key]);
		});
	};

	setEventListener = (event, callback) => {
		this.eventListeners[event] = callback;
		this.tomSelect.on(event, callback);
	};

	clear = () => {
		if (this.tomSelect != undefined) this.tomSelect.clear();
		this.setDefaultValue();
	};

	enable = (forced = false) => {
		if (this.defaultDisabled && !forced) return;

		if (this.tomSelect != undefined) this.tomSelect.enable();
		else this.element.disabled = false;
	};

	disable = () => {
		if (this.tomSelect != undefined) this.tomSelect.disable();
		else this.element.disabled = true;
	};

	destroy = () => {
		if (this.tomSelect != undefined) this.tomSelect.destroy();
	};

	setExtraLoadParam = (key, value) => {
		this.loadParams[key] = value;
	};

	detectParentAndSetFunctions = () => {
		setTimeout(() => {
			if (!this.parentSelect)
				this.parentSelect = Select.INSTANCES[this.parent];

			if (this.parentSelect) {
				this.parentSelect.setEventListener("change", (value) => {
					this.data.items = [];
					this.setExtraLoadParam(this.parentSelect.id, value);
					this.reload();
				});
			}
		}, 500);
	};
}
