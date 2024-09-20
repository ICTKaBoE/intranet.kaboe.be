import Helpers from "./Helpers.js";

export default class Button {
	static TYPE_ICON = "icon";
	static TYPE_ICON_TEXT = "icon-text";
	static TYPE_TEXT = "text";
	static INSTANCES = {};

	constructor({ element = null, options = {} }) {
		this.options = options;
		if (element !== null) this.element = element;
		else {
			this.element = document.createElement("button");
			this.create();
		}

		this.element.id = Helpers.generateId("btn");
		this.id = this.element.id;
	}

	static ScanAndCreate = () => {
		$("button,.btn*").each((ids, el) => {
			if (!Button.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Button.INSTANCES[el.getAttribute("id")] = new Button({
					element: el,
				});
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("btn")) id = `btn${id}`;
		return Button.INSTANCES[id] || false;
	};

	create = () => {
		this.element.type = "button";
		this.element.classList.add("btn");
		if (this.options.bgColor || false)
			this.element.classList.add(`btn-${this.options.bgColor}`);

		if (this.options.title || false) {
			this.element.title = this.options.title;
			this.element.dataset.bsToggle = "tooltip";
			this.element.dataset.bsPlacement = "top";
		}

		if (this.options.onclick || false)
			this.element.addEventListener("click", () => {
				if (this.options.onclick instanceof Function)
					this.options.onclick();
				else window[this.options.onclick]();
			});

		if (this.options.modal || false)
			this.element.addEventListener("click", () => {
				Helpers.closeAllModals();
				Helpers.toggleModal(this.options.modal);
			});

		switch (this.options.type || Button.TYPE_TEXT) {
			case Button.TYPE_ICON:
				{
					let icon = document.createElement("i");
					icon.classList.add("icon", "ti", `ti-${this.options.icon}`);

					this.element.classList.add("btn-icon");
					this.element.appendChild(icon);
				}
				break;

			case Button.TYPE_ICON_TEXT:
				{
					let icon = document.createElement("i");
					icon.classList.add("icon", "ti", `ti-${this.options.icon}`);

					this.element.appendChild(icon);
					this.element.innerHTML += this.options.text;
				}
				break;

			default:
				this.element.innerHTML = this.options.text;
				break;
		}
	};

	setOnClick = (funcName) => {
		this.element.addEventListener("click", () => {
			window[funcName]();
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
