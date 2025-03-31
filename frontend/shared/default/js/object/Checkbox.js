import Helpers from "./Helpers.js";

export default class Checkbox {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.type = this.element.dataset.type || "radio";
		this.name = this.element.dataset.name || false;
		this.value = this.element.dataset.value?.split("|") || "true";
		this.text = this.element.dataset.text?.split("|") || false;
		this.defaultValue = this.element.dataset.defaultValue || false;
		this.onChange = this.element.dataset.onChange || false;

		if (typeof this.value === "string") this.value = [this.value];

		this.inputs = {};

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='checkbox']").each((ids, el) => {
			if (!Checkbox.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Checkbox.INSTANCES[el.getAttribute("id")] = new Checkbox(el);
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("chb"))
			id = `chb${
				String(id).charAt(0).toUpperCase() + String(id).slice(1)
			}`;
		return Checkbox.INSTANCES[id] || false;
	};

	init = () => {
		this.build();
		if (this.defaultValue) this.setValue(this.defaultValue);
	};

	build = () => {
		for (let index = 0; index < this.value.length; index++) {
			const value = this.value[index];
			const text = this.text[index] || value;

			let label = document.createElement("label");
			label.classList.add("form-check");

			this.inputs[value] = document.createElement("input");
			this.inputs[value].name = this.name;
			this.inputs[value].type = this.type;
			this.inputs[value].dataset.value = value;
			this.inputs[value].classList.add("form-check-input");
			this.inputs[value].role = "checkbox";
			this.inputs[value].dataset.id = this.id;

			if (this.onChange) {
				this.inputs[value].addEventListener("change", () => {
					if (this.onChange instanceof Function) this.onChange();
					else window[this.onChange]();
				});
			}

			if (this.type === "checkbox") this.inputs[value].id = this.name;
			label.appendChild(this.inputs[value]);

			let span = document.createElement("span");
			span.innerHTML = text;
			span.classList.add("form-check-label");
			label.appendChild(span);

			this.element.appendChild(label);
		}
	};

	enable = () => {
		this.element.removeAttribute("disabled");
	};

	disable = () => {
		this.element.setAttribute("disabled", null);
	};

	setValue = (value) => {
		if (value == "1") value = true;
		if (value == "0") value = false;

		Object.keys(this.inputs).forEach((item) => {
			if (String(value) === item) this.inputs[item].checked = true;
			else this.inputs[item].checked = false;
		});

		if (this.onChange) {
			if (this.onChange instanceof Function) this.onChange();
			else window[this.onChange]();
		}
	};

	getValue = () => {
		return (
			Object.keys(this.inputs).filter(
				(item) => this.inputs[item].checked
			)[0] || false
		);
	};

	getName = () => this.name;
}
