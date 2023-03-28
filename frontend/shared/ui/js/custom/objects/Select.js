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
		this.loadValue = this.element.dataset.loadValue || 'id';
		this.loadLabel = this.element.dataset.loadLabel || false;
		this.loadParams = {};
		this.defaultValue = this.element.dataset.defaultValue || false;
		this.multiple = this.element.hasAttribute("multiple");
		this.parent = this.element.dataset.parentSelect || false;
		this.disabled = this.element.hasAttribute("disabled") || false;

		this.init();
	}

	static ScanAndCreate() {
		$("select").each((ids, el) => {
			Select.INSTANCES[el.id] = new Select(el);
		});
	}

	init = async () => {
		this.element.setAttribute("role", "select");
		this.element.removeAttribute("disabled");

		this.createSelect();
		this.disable();
		if (this.parent) this.detectParentAndSetFunctions();
		await this.loadSelect();
		this.setDefaultValue();
		if (!this.disabled) this.enable();
	};

	reload = async () => {
		this.disable();
		this.clear();
		this.destroy();
		this.createSelect();
		if (this.parent) this.detectParentAndSetFunctions();
		await this.loadSelect();
		this.setDefaultValue();
		if (!this.disabled) this.enable();
	};

	createSelect = () => {
		let settings = {
			plugins: this.multiple ? ['remove_button', 'checkbox_options'] : [],
			hideSelected: false,
			maxOptions: null,
			maxItems: this.multiple ? null : 1,
			copyClassesToDropdown: false,
			dropdownClass: 'dropdown-menu ts-dropdown',
			optionClass: 'dropdown-item',
			controlInput: '<input>',
			create: false,
			render: {},
			searchField: ['text']
		};

		if (this.render.item) settings.render.item = (data, escape) => { return window[this.render.item](data, escape); };
		if (this.render.option) settings.render.option = (data, escape) => { return window[this.render.option](data, escape); };
		if (this.onChange) settings.onChange = (value) => { window[this.onChange](value); };

		if (this.loadSource && this.loadValue && this.loadLabel) {
			settings.valueField = this.loadValue;
			settings.labelField = this.loadLabel;
			settings.searchField = [this.loadLabel];

			settings.load = (query, callback) => {
				$.get(this.loadSource, this.loadParams).done(data => {
					callback(data.items);
				}).fail(() => {
					callback();
				});
			};
		}

		this.tomSelect = new TomSelect(this.element, settings);
	};

	loadSelect = () => {
		if (!this.loadSource) return;

		return new Promise((resolve, reject) => {
			this.tomSelect.on("load", (data) => {
				resolve();
			});

			this.tomSelect.load();
		});
	};

	setDefaultValue = () => {
		if (this.defaultValue) {
			this.setValue(this.defaultValue);
		}
	};

	setValue = (value) => {
		this.tomSelect.setValue(value, false);
	};

	getValue = () => {
		return this.tomSelect.getValue();
	};

	setEventListener = (event, callback) => {
		this.tomSelect.on(event, callback);
	};

	clear = () => {
		if (this.tomSelect != undefined) this.tomSelect.clear();
	};

	enable = () => {
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
		if (!this.parentSelect) this.parentSelect = Select.INSTANCES[this.parent];

		if (this.parentSelect) {
			this.parentSelect.setEventListener('change', value => {
				this.setExtraLoadParam('parentValue', value);
				this.reload();
			});

		}
	};
}