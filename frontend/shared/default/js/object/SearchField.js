import Table from "./Table.js";
import List from "./List.js";

export default class SearchField {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.init();
	}

	static ScanAndCreate() {
		$("*[role='searchField']").each((ids, el) => {
			if (!SearchField.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				SearchField.INSTANCES[el.getAttribute("id")] = new SearchField(
					el
				);
		});
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
