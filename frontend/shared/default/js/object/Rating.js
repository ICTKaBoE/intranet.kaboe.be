import MasterObject from "../MasterObject.js";

export default class Rating extends MasterObject {
	static OBJ_SELECTOR = "div[role='rating']";
	static OBJ_ID_PREFIX = "rtn";

	constructor(element) {
		super();

		this.element = element;
		this.id = this.element.id || false;
		this.icon = this.element.dataset.icon || "star";
		this.color = this.element.dataset.color || "green";
		this.maximum = this.element.dataset.maximum || 5;
		this.value = this.element.dataset.value || 0;
		this.mode = this.element.dataset.mode || "ro";
		this.size = this.element.dataset.size || false;

		this.init();
	}

	init = () => {
		this.container = document.createElement("div");

		for (let i = 1; i <= this.maximum; i++) {
			let icon = document.createElement("i");
			icon.classList.add(
				"ti",
				`ti-${this.icon}${i <= this.value ? "-filled" : ""}`,
				`text-${this.color}`
			);
			if (this.size) icon.classList.add(`fs-${this.size}`);
			this.container.appendChild(icon);
		}

		this.element.appendChild(this.container);
	};
}
