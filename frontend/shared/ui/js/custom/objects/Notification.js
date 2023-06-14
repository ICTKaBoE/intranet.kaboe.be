import Helpers from "./Helpers.js";

export default class Notification {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.noNotificationText = this.element.dataset.noNotificationText || "No updates";

		this.init();
	}

	static ScanAndCreate() {
		$("div[role='notification']").each((ids, el) => {
			Notification.INSTANCES[el.id] = new Notification(el);
		});
	}

	init = () => {
		this.createStructure();
		this.showNoNotifications();
	};

	createStructure = () => {
		if (!this.element.classList.contains("d-none")) this.element.classList.add("d-none");
		if (!this.element.classList.contains("d-md-flex")) this.element.classList.add("d-md-flex");

		let container = document.createElement("div");
		container.classList.add("nav-item", "dropdown", "d-none", "d-md-flex", "me-3");

		let bell = document.createElement("a");
		bell.href = "#";
		bell.classList.add("nav-link", "px-0");
		bell.dataset.bsToggle = "dropdown";
		bell.tabIndex = -1;
		bell.ariaLabel = "Show notifications";
		bell.innerHTML = Helpers.loadIcon("bell");

		this.bellBadge = document.createElement("span");
		this.bellBadge.classList.add("badge", "bg-red", "d-none");

		bell.appendChild(this.bellBadge);
		container.appendChild(bell);

		let dropdown = document.createElement("div");
		dropdown.classList.add("dropdown-menu", "dropdown-menu-arrow", "dropdown-menu-end", "dropdown-menu-card");

		this.dropdownCard = document.createElement("div");
		this.dropdownCard.classList.add("card");

		dropdown.appendChild(this.dropdownCard);
		container.appendChild(dropdown);

		this.element.appendChild(container);
	};

	clear = () => {
		this.dropdownCard.innerHTML = "";
	};

	showBadge = (amount = 0) => {
		if (this.bellBadge.classList.contains("d-none")) this.bellBadge.classList.remove("d-none");
		this.bellBadge.innerHTML = amount;
	};

	hideBadge = () => {
		if (!this.bellBadge.classList.contains("d-none")) this.bellBadge.classList.add("d-none");
	};

	showNoNotifications = () => {
		this.hideBadge();
		this.clear();

		let cardHeader = document.createElement("div");
		cardHeader.classList.add("card-header");

		let cardTitle = document.createElement("h3");
		cardTitle.classList.add("card-title");
		cardTitle.innerHTML = this.noNotificationText;

		cardHeader.appendChild(cardTitle);
		this.dropdownCard.appendChild(cardHeader);
	};
}