import Helpers from "./Helpers.js";

export default class Toast {
	static INSTANCE = null;

	constructor() {
		this.init();
	}

	static Create = () => {
		Toast.INSTANCE = new Toast;
	};

	init = async () => {
		this.createStructure();
	};

	createStructure = () => {
		if (!this.container) {
			this.container = document.createElement("DIV");
			this.container.classList.add("toast-container", "position-fixed", "bottom-0", "end-0", "p-3");
			document.body.appendChild(this.container);
		}
	};

	show = (creator, content) => {
		let toast = document.createElement("div");
		toast.classList.add("toast", "mb-2");
		toast.role = "alert";
		toast.ariaLive = "assertive";
		toast.ariaAtomic = true;

		let toastHeader = document.createElement("div");
		toastHeader.classList.add("toast-header");

		let strong = document.createElement("strong");
		strong.classList.add("me-auto");
		strong.innerHTML = creator;

		let btn = document.createElement("button");
		btn.classList.add("btn-close");
		btn.type = "button";
		btn.dataset.bsDismiss = "toast";
		btn.ariaLabel = "Close";

		toastHeader.appendChild(strong);
		toastHeader.appendChild(btn);
		toast.appendChild(toastHeader);

		let toastBody = document.createElement("div");
		toastBody.classList.add("toast-body");
		toastBody.innerHTML = content;

		toast.appendChild(toastBody);

		this.container.appendChild(toast);
		(bootstrap.Toast.getOrCreateInstance(toast)).show();
	};
}