import Helpers from "./Helpers.js";

export default class Signage {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this.code = this.element.dataset.code || false;

		this.slides = [];

		this.extraData = {};
		if (this.code) this.extraData.code = this.code;

		this.currentSlide = 0;

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='signage']").each((ids, el) => {
			if (!Signage.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Signage.INSTANCES[el.getAttribute("id")] = new Signage(el);
		});
	}

	static GetInstance = (id) => {
		if (!id.startsWith("sgn")) id = `sgn${id}`;
		return Signage.INSTANCES[id] || false;
	};

	static ReloadAll = () => {
		for (const sgn in Signage.INSTANCES) {
			Signage.INSTANCES[sgn].reload();
		}
	};

	init = async () => {
		this.createStructure();
		await this.getData();
		this.loadData();
		await this.loop();
	};

	reload = async () => {
		this.clear();
		await this.getData();
		this.loadData();
		await this.loop();
	};

	createStructure = () => {
		this.element.classList.add("container");
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done((data) => {
			Helpers.processRequestResponse(data);
			this.data = data;
		});
	};

	loadData = () => {
		if (this.data.items.length == 0) return;

		for (let i in this.data.items) {
			let item = this.data.items[i];

			this.slides[i] = {
				type: item.linked.media.type,
				container: null,
				content: null,
				duration: item.duration,
			};
			this.slides[i].container = document.createElement("div");
			this.slides[i].container.classList.add(
				"row",
				"align-items-center",
				"p-5",
				"vh-100",
				"d-none"
				// "vh-100"
			);

			switch (this.slides[i].type) {
				case "I":
					{
						this.slides[i].content = document.createElement("img");
						this.slides[i].content.src =
							item.linked.media.formatted.link;
						this.slides[i].content.classList.add(
							"h-100",
							"w-auto",
							"m-auto"
						);

						this.slides[i].container.appendChild(
							this.slides[i].content
						);
					}
					break;

				case "V":
					{
						this.slides[i].content =
							document.createElement("video");
						this.slides[i].content.classList.add(
							"h-auto",
							"w-auto",
							"m-auto"
						);

						let s = document.createElement("source");
						s.src = item.linked.media.formatted.link;
						this.slides[i].content.appendChild(s);

						this.slides[i].container.appendChild(
							this.slides[i].content
						);
					}
					break;

				case "L":
					{
						this.slides[i].content =
							document.createElement("iframe");
						this.slides[i].content.src =
							item.linked.media.formatted.link;
						this.slides[i].content.classList.add(
							"h-100",
							"w-100",
							"m-auto"
						);
						this.slides[i].content.setAttribute(
							"allowTransparency",
							"true"
						);
						this.slides[i].content.setAttribute("scrolling", "no");
						this.slides[i].content.setAttribute("frameborder", 0);

						this.slides[i].container.appendChild(
							this.slides[i].content
						);
					}
					break;
			}

			this.element.appendChild(this.slides[i].container);
		}
	};

	loop = async () => {
		if (this.data.items.length == 0) return;

		this.setActiveSlide();
		await Helpers.sleep(this.slides[this.currentSlide].duration * 1000);

		if (this.currentSlide == this.slides.length - 1) this.reload();
		else {
			this.currentSlide++;
			this.loop();
		}
	};

	setActiveSlide = () => {
		for (let i in this.slides) {
			if (i == this.currentSlide) {
				this.slides[i].container.classList.remove("d-none");
				if (this.slides[i].type == "V") {
					this.slides[i].content.muted = "muted";
					this.slides[i].content.play();
				}
			} else {
				this.slides[i].container.classList.add("d-none");
				if (this.slides[i].type == "V") this.slides[i].content.pause();
			}
		}
	};

	clear = () => {
		this.currentSlide = 0;
		this.slides = [];
		this.element.innerHTML = "";
	};
}
