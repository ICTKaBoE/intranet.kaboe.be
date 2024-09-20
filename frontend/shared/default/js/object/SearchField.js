import Table from "./Table.js";

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

		this.input.onkeyup = (e) => {
			if (e.keyCode == 27) this.input.value = "";
			Table.SearchAll(this.input.value);
		};
	};

	enable = () => {
		this.input.disabled = false;
	};

	disable = () => {
		this.input.disabled = true;
	};
}
