import MasterObject from "../MasterObject.js";
import Helpers from "./Helpers.js";

export default class Button extends MasterObject {
	static OBJ_SELECTOR = "button,.btn*";
	static OBJ_ID_PREFIX = "btn";

	static TYPE_ICON = "icon";
	static TYPE_ICON_TEXT = "icon-text";
	static TYPE_TEXT = "text";

	constructor({ element = null, options = {} }) {
		super();

		this.options = options;
		if (element !== null) this.element = element;
		else this.element = document.createElement("button");

		this.create(element === null);

		this.id = this.element.id || Helpers.generateId("btn");
	}

	create = (full = false) => {
		if (full) {
			this.element.type = "button";
			this.element.classList.add("btn");

			if (this.options.bgColor || false)
				this.element.classList.add(`btn-${this.options.bgColor}`);

			if (this.options.title || false) {
				this.element.title = this.options.title;
				this.element.dataset.bsToggle = "tooltip";
				this.element.dataset.bsPlacement = "top";
			}
		}

		if (this.options.onclick || false)
			this.element.addEventListener("click", () => {
				if (this.options.onclick instanceof Function)
					this.options.onclick();
				else window[this.options.onclick]();
			});

		if (this.options.modal || false)
			this.element.addEventListener("click", () => {
				Helpers.toggleModal(this.options.modal);
			});

		if (full) {
			switch (this.options.type || Button.TYPE_TEXT) {
				case Button.TYPE_ICON:
					{
						let icon = document.createElement("i");
						icon.classList.add(
							"icon",
							"ti",
							`ti-${this.options.icon}`
						);

						this.element.classList.add("btn-icon");
						this.element.appendChild(icon);
					}
					break;

				case Button.TYPE_ICON_TEXT:
					{
						let icon = document.createElement("i");
						icon.classList.add(
							"icon",
							"ti",
							`ti-${this.options.icon}`
						);

						this.element.appendChild(icon);
						this.element.innerHTML += this.options.text;
					}
					break;

				default:
					this.element.innerHTML = this.options.text;
					break;
			}
		}
	};

	setOnClick = (func) => {
		this.element.addEventListener("click", () => {
			if (func instanceof Function) func();
			else window[func]();
		});
	};

	write = () => {
		return this.element;
	};

	enable = () => {
		this.element.removeAttribute("disabled");
	};

	disable = () => {
		this.element.setAttribute("disabled", null);
	};

	show = () => {
		this.element.classList.remove("d-none");
	};

	hide = () => {
		this.element.classList.add("d-none");
	};
}
