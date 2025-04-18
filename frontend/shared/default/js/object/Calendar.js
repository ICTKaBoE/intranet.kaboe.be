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

		this.extraData = {};

		this.editable = this.element.hasAttribute("data-editable");
		this.weekends = this.element.hasAttribute("data-weekends");
		this.allDaySlot = this.element.hasAttribute("data-all-day-slot");
		this.slotDuration = this.element.dataset.slotDuration || "00:30:00";
		this.slotMinTime = this.element.dataset.slotMinTime || false;
		this.slotMaxTime = this.element.dataset.slotMaxTime || false;

		if (String(this.source).charAt(0) == "[") {
			this.source = String(this.source).replace("[", "").replace("]", "");
			this.source = String(this.source).split(",");
		}

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='calendar']").each((ids, el) => {
			if (!Calendar.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Calendar.INSTANCES[el.getAttribute("id")] = new Calendar(el);
		});
	}

	static GetInstance = (id) => {
		if (!id.startsWith("cal")) id = `cal${id}`;
		return Calendar.INSTANCES[id] || false;
	};

	static ReloadAll = () => {
		for (const cal in Calendar.INSTANCES) {
			Calendar.INSTANCES[cal].reload();
		}
	};

	init = () => {
		this.createCalendar();
	};

	createCalendar = () => {
		let options = {
			initialView: this.view,
			weekends: this.weekends,
			locale: "nl",
			headerToolbar: {
				start: "prevYear,prev",
				center: "title",
				end: "today next,nextYear",
			},
		};

		if (this.view === "timeGridWeek") {
			options.allDaySlot = this.allDaySlot;
			options.slotDuration = this.slotDuration;
			options.nowIndicator = true;
			options.scrollTime = new Date().getHours() - 1 + ":00:00";
			options.editable = this.editable;
			options.selectable = true;
			options.expandRows = true;
		}

		if (this.slotMinTime) options.slotMinTime = this.slotMinTime;
		if (this.slotMaxTime) options.slotMaxTime = this.slotMaxTime;

		if (this.dateClick || this.dateSelect) {
			if (this.view === "timeGridWeek") {
				options.select = (info) => {
					window[this.dateSelect](info);
				};

				options.eventDrop = (info) => {
					window[this.dateSelect](info);
				};

				options.eventResize = (info) => {
					window[this.dateSelect](info);
				};

				options.eventClick = (info) => {
					window[this.dateClick](info);
				};
			} else {
				options.dateClick = (info) => {
					window[this.dateClick](info);
				};
			}
		}

		if (this.source) {
			if (Array.isArray(this.source)) {
				let sources = [];

				for (const source of this.source) {
					sources.push({
						url: source,
						extraParams: this.extraData,
					});
				}

				options.eventSources = this.source;
			} else
				options.events = {
					url: this.source,
					extraParams: this.extraData,
				};
		}

		this.elementObject = new FullCalendar.Calendar(this.element, options);
		this.elementObject.render();
	};

	reload = () => {
		if (!this.source) return;
		this.elementObject.refetchEvents();
	};

	addExtraData = (key, value) => {
		this.extraData[key] = value;
	};
}
