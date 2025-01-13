import Helpers from "./Helpers.js";

export default class ColorInput {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id;

		this.colors = this.element.dataset.colors.split("|") || [];
		this.name = this.element.dataset.inputName;
		this.defaultValue = this.element.dataset.defaultValue || false;

		this.inputs = [];

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='colorinput']").each((ids, el) => {
			if (!ColorInput.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				ColorInput.INSTANCES[el.getAttribute("id")] = new ColorInput(
					el
				);
		});
	};

	static GetInstance = (id) => {
		console.log(id);
		if (!id.startsWith("cin")) id = `cin${id}`;
		return ColorInput.INSTANCES[id] || false;
	};

	init = () => {
		this.createStructure();
		this.createOptions();
		if (this.defaultValue) this.setValue(this.defaultValue);
	};

	createStructure = () => {
		if (!this.element.classList.contains("row"))
			this.element.classList.add("row");
		if (!this.element.classList.contains("g-2"))
			this.element.classList.add("g-2");
	};

	createOptions = () => {
		this.colors.forEach((color) => {
			let colAuto = document.createElement("div");
			colAuto.classList.add("col-auto");

			let label = document.createElement("label");
			label.classList.add("form-colorinput");

			this.inputs[color] = document.createElement("input");
			this.inputs[color].name = this.name;
			this.inputs[color].type = "radio";
			this.inputs[color].dataset.value = color;
			this.inputs[color].classList.add("form-colorinput-input");
			label.appendChild(this.inputs[color]);

			let span = document.createElement("span");
			span.classList.add("form-colorinput-color");
			span.classList.add("rounded-circle");
			span.classList.add(`bg-${color}`);
			label.appendChild(span);

			colAuto.appendChild(label);
			this.element.appendChild(colAuto);
		});
	};

	enable = () => {
		this.element.removeAttribute("disabled");
	};

	disable = () => {
		this.element.setAttribute("disabled", null);
	};

	setValue = (value) => {
		Object.keys(this.inputs).forEach((color) => {
			if (value === color) this.inputs[color].checked = true;
			else this.inputs[color].checked = false;
		});
	};

	getValue = () => {
		return Object.keys(this.inputs).filter(
			(item) => this.inputs[item].checked
		)[0];
	};

	getName = () => this.name;
}
