export default class Calendar {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;
		this.dateClick = this.element.dataset.dateClick || false;
		this.source = this.element.dataset.source || false;
		this.action = this.element.dataset.action || false;

		this.range = {};
		this.range.start = this.element.dataset.rangeStart || false;
		this.range.end = this.element.dataset.rangeEnd || false;

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

		if (this.dateClick) options.dateClick = (info) => {
			if (this.range.start || this.range.end) {
				if (Date.parse(info.dateStr) < Date.parse(this.range.start)) return;
				if (Date.parse(info.dateStr) > Date.parse(this.range.end)) return;
			}

			window[this.dateClick](info);
		};

		if (this.source) {
			if (Array.isArray(this.source)) options.eventSources = this.source;
			else options.events = this.source;
		}

		this.elementObject = new FullCalendar.Calendar(this.element, options);
		this.elementObject.render();
	};

	reload = () => {
		this.elementObject.refetchEvents();
	};
}