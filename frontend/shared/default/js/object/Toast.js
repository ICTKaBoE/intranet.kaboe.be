import MasterObject from "../MasterObject.js";
import Helpers from "./Helpers.js";

export default class Toast extends MasterObject {
	static OBJ_SELECTOR = ".toast-container";

	constructor(element) {
		super();

		this.element = element;

		this.init();
	}

	init = async () => {
		if (this.element.dataset?.notifications) this.getNotifications();
	};

	show = (toasts) => {
		if (!Array.isArray(toasts)) toasts = [toasts];

		for (const toast of toasts) {
			this._show(toast.message, toast?.type, toast?.link, toast?.delay);
		}
	};

	_show = (message, type = "valid", link = null, delay = 5000) => {
		let toast = document.createElement("div");
		toast.classList.add(
			"toast",
			"mb-2",
			"align-items-center",
			`text-bg-${
				type === "normal"
					? "primary"
					: type === "valid"
					? "green"
					: "red"
			}`,
			"border-0"
		);
		toast.role = "alert";
		toast.ariaLive = "assertive";
		toast.ariaAtomic = true;
		if (delay) toast.dataset.bsDelay = delay;
		if (link) {
			toast.role = "button";
			toast.onclick = () => {
				window.location.href = link;
			};
		}

		let dflex = document.createElement("div");
		dflex.classList.add("d-flex");

		let toastBody = document.createElement("div");
		toastBody.classList.add("toast-body");
		toastBody.innerHTML = message;

		dflex.appendChild(toastBody);

		let btn = document.createElement("button");
		btn.classList.add("btn-close", "btn-close-white", "me-2", "m-auto");
		btn.type = "button";
		btn.dataset.bsDismiss = "toast";
		btn.ariaLabel = "Close";

		dflex.appendChild(btn);

		toast.appendChild(dflex);
		this.element.appendChild(toast);
		bootstrap.Toast.getOrCreateInstance(toast).show();
	};

	getNotifications = () => {
		Helpers.request({
			url: this.element.dataset.notifications,
			always: (data) => {
				Helpers.processRequestResponse(data);
			},
		});

		setInterval(() => {
			Helpers.request({
				url: this.element.dataset.notifications,
				always: (data) => {
					Helpers.processRequestResponse(data);
				},
			});
		}, 60000);
	};
}
