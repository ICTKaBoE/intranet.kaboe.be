import Table from "./Table.js";
import List from "./List.js";
import MasterObject from "../MasterObject.js";

export default class SearchField extends MasterObject {
	static OBJ_SELECTOR = "*[role='searchField']";

	constructor(element) {
		super();

		this.element = element;
		this.id = this.element.id || false;

		this.init();
	}

	init = () => {
		this.input = $(this.element).find("input")[0];
		this.input.addEventListener("input", this.debounce(this.search));
	};

	enable = () => {
		this.input.disabled = false;
	};

	disable = () => {
		this.input.disabled = true;
	};

	debounce = (ev, delay = 250) => {
		let timer;

		return () => {
			clearTimeout(timer);

			timer = setTimeout(() => {
				ev.call(this);
			}, delay);
		};
	};

	search = () => {
		Table.SearchAll(this.input.value);
		List.SearchAll(this.input.value);
	};
}
