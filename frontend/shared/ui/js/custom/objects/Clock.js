export default class Clock {
	static INSTANCES = {};

	constructor(element) {
		this.clock = element;
		this.id = this.clock.id || false;

		this.showDate = this.clock.hasAttribute("data-show-date") || false;
		this.showTime = this.clock.hasAttribute("data-show-time") || false;
		this.backgroundColor = this.clock.dataset.backgroundColor;

		this.init();
	}

	static ScanAndCreate() {
		$("*[role='clock']").each((ids, el) => {
			Clock.INSTANCES[el.id] = new Clock(el);
		});
	}

	init = () => {
		this.clock.style.backgroundColor = this.backgroundColor;

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
		this.clock.innerHTML = this.getCurrentTime();
	};
}