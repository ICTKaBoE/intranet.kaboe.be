import Helpers from "./Helpers.js";

export default class Button {
	static INSTANCES = {};

	constructor({ type, text, icon, backgroundColor, classes, onclick, params } = {}) {
		this.type = type || "text";
		this.text = text || null;
		this.icon = (this.type === 'icon' || this.type === 'text-icon' || this.type === 'icon-text' || this.type === 'icon-title') ? icon : false;
		this.backgroundColor = backgroundColor || false;
		this.classes = classes || [];
		this.onclick = onclick || false;
		this.params = params || [];

		this.init();
	}

	init = () => {
		this.button = document.createElement("button");
		this.button.setAttribute("type", "button");
		this.button.classList.add("btn");

		if (this.backgroundColor) this.button.classList.add(`btn-${this.backgroundColor}`);
		if (this.onclick) {
			if (this.params) this.button.onclick = () => window[this.onclick](...this.params);
			else this.button.onclick = () => window[this.onclick]();
		}
		if (this.classes) this.button.classList.add(...this.classes);

		if (this.icon) {
			if (this.type === "icon") this.button.innerHTML = Helpers.loadIcon(this.icon);
			else if (this.type === "text-icon") this.button.innerHTML = `${this.text} ${Helpers.loadIcon(this.icon)}`;
			else if (this.type === "icon-text") this.button.innerHTML = `${Helpers.loadIcon(this.icon)} ${this.text}`;
			else if (this.type === "icon-title") {
				this.button.innerHTML = Helpers.loadIcon(this.icon);
				this.button.title = this.text;
			}
		} else this.button.innerHTML = this.text;
	};

	enable = () => {
		this.button.removeAttribute("disabled");
	};

	disable = () => {
		this.button.setAttribute("disabled", null);
	};

	write = () => {
		return this.button;
	};
}