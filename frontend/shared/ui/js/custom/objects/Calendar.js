export default class Calendar {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;
		this.view = this.element.dataset.view || "dayGridMonth";
		this.dateClick = this.element.dataset.dateClick || false;
		this.dateSelect = this.element.dataset.dateSelect || false;
		this.source = this.element.dataset.source || false;
		this.action = this.element.dataset.action || false;

		this.allDaySlot = this.element.hasAttribute("data-all-day-slot");
		this.slotDuration = this.element.dataset.slotDuration || "00:30:00";

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
			initialView: this.view,
			locale: 'nl',
			headerToolbar: {
				start: 'prevYear,prev',
				center: 'title',
				end: 'today next,nextYear'
			},
		};

		if (this.view === "timeGridWeek") {
			options.allDaySlot = this.allDaySlot;
			options.slotDuration = this.slotDuration;
			options.nowIndicator = true;
			options.scrollTime = (new Date()).getHours() - 1 + ":00:00";
			options.editable = true;
			options.selectable = true;
		}

		if (this.dateClick || this.dateSelect) {
			if (this.view === "timeGridWeek") {
				options.select = (info) => {
					if (this.range.start || this.range.end) {
						if (Date.parse(info.startStr.split("T")[0]) < Date.parse(this.range.start) ||
							Date.parse(info.endStr.split("T")[0]) > Date.parse(this.range.end)) return;
					}

					window[this.dateSelect](info);
				};

				options.eventDrop = (info) => {
					if (this.range.start || this.range.end) {
						if (Date.parse(info.event.startStr.split("T")[0]) < Date.parse(this.range.start) ||
							Date.parse(info.event.endStr.split("T")[0]) > Date.parse(this.range.end)) {
							info.revert();
							return;
						};
					}

					window[this.dateSelect](info);
				};

				options.eventResize = (info) => {
					if (this.range.start || this.range.end) {
						if (Date.parse(info.event.startStr.split("T")[0]) < Date.parse(this.range.start) ||
							Date.parse(info.event.endStr.split("T")[0]) > Date.parse(this.range.end)) {
							info.revert();
							return;
						};
					}

					window[this.dateSelect](info);
				};

				options.eventClick = (info) => {
					window[this.dateClick](info);
				};
			} else {
				options.dateClick = (info) => {
					if (this.range.start || this.range.end) {
						if (Date.parse(info.dateStr.split("T")[0]) < Date.parse(this.range.start)) return;
						if (Date.parse(info.dateStr.split("T")[0]) > Date.parse(this.range.end)) return;
					}

					window[this.dateClick](info);
				};
			}
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