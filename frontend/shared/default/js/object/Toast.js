export default class Toast {
	static INSTANCE = null;

	constructor() {
		this.init();
	}

	static Create = () => {
		Toast.INSTANCE = new Toast();
	};

	init = async () => {
		this.getToastContainer();
	};

	getToastContainer = () => {
		this.container = $(".toast-container")[0];
	};

	show = (toasts) => {
		if (!Array.isArray(toasts)) toasts = [toasts];

		for (const toast of toasts) {
			this._show(toast.message, toast.type);
		}
	};

	_show = (message, type = "valid") => {
		let toast = document.createElement("div");
		toast.classList.add(
			"toast",
			"mb-2",
			"align-items-center",
			`text-bg-${type === "valid" ? "green" : "red"}`,
			"border-0"
		);
		toast.role = "alert";
		toast.ariaLive = "assertive";
		toast.ariaAtomic = true;

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
		this.container.appendChild(toast);
		bootstrap.Toast.getOrCreateInstance(toast).show();
	};
}
