import Helpers from "./Helpers.js";

export default class DatePicker {
	static INSTANCES = {};

	constructor(element) {
		this.input = element;
		this.id = this.input.id || false;

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='datepicker']").each((ids, el) => {
			DatePicker.INSTANCES[el.id] = new DatePicker(el);
		});
	};

	init = () => {
		this.createPicker();
	};

	createPicker = () => {
		let settings = {
			element: this.input,
			lang: 'nl-BE',
			buttonText: {
				previousMonth: Helpers.loadIcon("chevron-left"),
				nextMonth: Helpers.loadIcon("chevron-right"),
			}
		};

		this.litePicker = new Litepicker(settings);
	};

	setDate = (date) => {
		this.litePicker.setDate(date);
	};
}