import Document from "./Document.js";

export default class Calendar {
	static INSTANCES = {};

	constructor(element) {
		this.calendar = element;
		this.id = this.calendar.id || false;
		this.dateClick = this.calendar.dataset.dateClick || false;
		this.source = this.calendar.dataset.source || false;
		this.action = this.calendar.dataset.action || false;

		this.range = {};
		this.range.start = this.calendar.dataset.rangeStart || false;
		this.range.end = this.calendar.dataset.rangeEnd || false;

		if (String(this.source).charAt(0) == '[') {
			this.source = String(this.source).replace("[", "").replace("]", "");
			this.source = String(this.source).split(",");
		}

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='calendar']").each((ids, el) => {
			Calendar.INSTANCES[el.id] = new Calendar(el);
		});
	}

	init = () => {
		this.createCalendar();
	};

	createCalendar = () => {
		let options = {
			initialView: 'dayGridMonth',
			selectable: true,
			locale: 'nl',
			headerToolbar: {
				start: 'prevYear,prev',
				center: 'title',
				end: 'today next,nextYear'
			}
		};

		if (this.dateClick) options.dateClick = (info) => { window[this.dateClick](info); };
		if (this.source) {
			if (Array.isArray(this.source)) options.eventSources = this.source;
			else options.events = this.source;
		}

		if (this.range.start || this.range.end) {
			options.validRange = {};
			if (this.range.start) options.validRange.start = this.range.start;
			if (this.range.end) options.validRange.end = this.range.end;
		}

		this.calendarObject = new FullCalendar.Calendar(this.calendar, options);
		this.calendarObject.render();
	};

	reload = () => {
		this.calendarObject.refetchEvents();
	};
}