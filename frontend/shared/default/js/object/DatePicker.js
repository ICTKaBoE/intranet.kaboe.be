import MasterObject from "../MasterObject.js";

export default class DatePicker extends MasterObject {
	static OBJ_SELECTOR = "[role='datepicker']";

	constructor(element) {
		super();

		this.element = element;
		this.id = this.element.id || false;

		this.init();
	}

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

	setDateNow = () => {
		this.setDate(new Date().toISOString().split("T")[0]);
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
