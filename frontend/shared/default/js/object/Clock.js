export default class Clock {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.showDate = this.element.hasAttribute("data-show-date") || false;
		this.showTime = this.element.hasAttribute("data-show-time") || false;
		this.backgroundColor = this.element.dataset.backgroundColor;

		this.init();
	}

	static ScanAndCreate() {
		$("*[role='clock']").each((ids, el) => {
			if (!Clock.INSTANCES.hasOwnProperty(el.getAttribute("id"))) Clock.INSTANCES[el.getAttribute("id")] = new Clock(el);
		});
	}

	init = () => {
		this.element.style.backgroundColor = this.backgroundColor;

		this.displayTime();
		setInterval(this.displayTime, 500);
	};

	getCurrentTime = () => {
		let today = new Date();
		let string = "";

		if (this.showDate) {
			let day = today.getDate();
			let month = today.getMonth();
			let year = today.getFullYear();
			string += `${day < 10 ? '0' : ''}${day}/${month + 1 < 10 ? '0' : ''}${month + 1}/${year}`;
		}

		if (this.showTime) {
			let hours = today.getHours();
			let minutes = today.getMinutes();
			let seconds = today.getSeconds();
			string += ` ${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
		}

		return string;
	};

	displayTime = () => {
		this.element.innerHTML = this.getCurrentTime();
	};
}