import Helpers from "./Helpers.js";

export default class DatePicker {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='datepicker']").each((ids, el) => {
			if (!DatePicker.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				DatePicker.INSTANCES[el.getAttribute("id")] = new DatePicker(
					el
				);
		});
	};

	static GetInstance = (id) => {
		// if (!id.startsWith("cin")) id = `cin${id}`;
		return DatePicker.INSTANCES[id] || false;
	};

	init = () => {
		this.element.autocomplete = "off";
		this.createPicker();
	};

	createPicker = () => {
		let settings = {
			element: this.element,
			lang: "nl-BE",
			buttonText: {
				previousMonth: '<i class="icon ti ti-chevron-left"></i>',
				nextMonth: '<i class="icon ti ti-chevron-right"></i>',
			},
		};

		this.litePicker = new Litepicker(settings);
	};

	setDate = (date) => {
		this.litePicker.setDate(date);
	};

	getDate = () => {
		return this.litePicker.getDate();
	};

	reload = () => {
		this.litePicker.clearSelection();
	};
}
