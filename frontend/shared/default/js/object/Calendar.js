import MasterObject from "../MasterObject.js";

export default class Calendar extends MasterObject {
	static OBJ_SELECTOR = "div[role='calendar']";
	static OBJ_ID_PREFIX = "cal";

	constructor(element) {
		super();

		this.element = element;
		this.id = this.element.id || false;
		this.view = this.element.dataset.view || "dayGridMonth";
		this.dateClick = this.element.dataset.dateClick || false;
		this.dateSelect = this.element.dataset.dateSelect || false;
		this.source = this.element.dataset.source || false;
		this.action = this.element.dataset.action || false;

		this.extraData = {};

		this.editable = this.element.hasAttribute("data-editable");
		this.droppable = this.element.hasAttribute("data-droppable");
		this.onDrop = this.element.dataset.onDrop || false;
		this.eventDataFormat = this.element.dataset.eventDataFormat || false;
		this.eventRemove = this.element.dataset.eventRemove || false;
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

	init = () => {
		this.createCalendar();
	};

	createCalendar = () => {
		let options = {
			initialView: this.view,
			weekends: this.weekends,
			locale: "nl",
			firstDay: 1,
			headerToolbar: {
				start: "prevYear,prev",
				center: "title",
				end: "today next,nextYear",
			},
			editable: this.editable,
			droppable: this.droppable,
			eventContent: function (info) {
				return {
					html: `<div class="fc-event-title">${info.event.title}</div>`,
				};
			},
		};

		if (this.view === "timeGridWeek") {
			options.allDaySlot = this.allDaySlot;
			options.slotDuration = this.slotDuration;
			options.nowIndicator = true;
			options.scrollTime = new Date().getHours() - 1 + ":00:00";
			options.selectable = true;
			options.expandRows = true;
		}

		if (this.slotMinTime) options.slotMinTime = this.slotMinTime;
		if (this.slotMaxTime) options.slotMaxTime = this.slotMaxTime;

		if (this.droppable) {
			options.drop = (info) => {
				window[this.onDrop](info);
			};

			options.eventDrop = (info) => {
				window[this.onDrop](info);
			};

			if (this.eventRemove) {
				options.eventDragStop = (e) => window[this.eventRemove](e);
			}

			let dragOptions = { itemSelector: ".drop-event" };
			if (this.eventDataFormat) {
				dragOptions.eventData = (eventEl) =>
					window[this.eventDataFormat](eventEl);
			}

			new FullCalendar.Draggable(
				document.getElementById(this.element.dataset.eventContainer),
				dragOptions
			);
		}

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
			options.events = (info, successCallback, failureCallback) => {
				let fetches = [];

				if (Array.isArray(this.source)) {
					for (const source of this.source) {
						fetches.push(
							fetch(
								source +
									"?" +
									new URLSearchParams(this.extraData),
								{ credentials: "include" }
							).then((resp) => resp.json())
						);
					}
				} else
					fetches.push(
						fetch(
							this.source +
								"?" +
								new URLSearchParams(this.extraData),
							{ credentials: "include" }
						).then((resp) => resp.json())
					);

				Promise.all(fetches)
					.then((...datas) => {
						let d = [];
						for (const data of datas[0]) d = d.concat(data);
						successCallback(d);
					})
					.catch((error) => {
						failureCallback(error);
					});
			};
		}

		this.elementObject = new FullCalendar.Calendar(this.element, options);
		this.elementObject.render();
	};

	revert = () => {
		this.elementObject.revert();
	};

	reload = () => {
		if (!this.source) return;
		this.elementObject.refetchEvents();
	};

	addExtraData = (key, value) => {
		this.extraData[key] = value;
	};
}
