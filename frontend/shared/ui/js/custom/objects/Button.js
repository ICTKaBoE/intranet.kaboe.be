import Helpers from "./Helpers.js";

export default class Button {
	static INSTANCES = {};

	constructor({ type, text, icon, backgroundColor, onclick } = {}) {
		this.type = type || "simple";
		this.text = text;
		this.icon = (this.type === 'icon' || this.type === 'text-icon' || this.type === 'icon-text') ? icon : false;
		this.backgroundColor = backgroundColor || false;
		this.onclick = onclick || false;

		this.init();
	}

	init = () => {
		this.button = document.createElement("button");
		this.button.setAttribute("type", "button");
		this.button.classList.add("btn");

		if (this.backgroundColor) this.button.classList.add(`btn-${this.backgroundColor}`);
		if (this.onclick) this.button.onclick = () => window[this.onclick]();

		if (this.type === "icon") this.button.innerHTML = Helpers.loadIcon(this.icon);
		else if (this.type === "text-icon") this.button.innerHTML = `${this.text} ${Helpers.loadIcon(this.icon)}`;
		else if (this.type === "icon-text") this.button.innerHTML = `${Helpers.loadIcon(this.icon)} ${this.text}`;
		else this.button.innerHTML = this.text;
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