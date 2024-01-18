export default class Toast {
	static INSTANCE = null;

	constructor() {
		this.init();
	}

	static Create = () => {
		Toast.INSTANCE = new Toast;
	};

	init = async () => {
		this.getToastContainer();
	};

	getToastContainer = () => {
		this.container = $(".toast-container")[0];
	};

	show = (toasts) => {
		console.log(toasts);

		if (!Array.isArray(toasts)) toasts = [toasts];


		for (const toast of toasts) {
			this._show(toast.title, toast.message, toast.type);
		}
	};

	_show = (title, message, type = "valid") => {
		let toast = document.createElement("div");
		toast.classList.add("toast", "mb-2");
		toast.role = "alert";
		toast.ariaLive = "assertive";
		toast.ariaAtomic = true;

		let toastHeader = document.createElement("div");
		toastHeader.classList.add("toast-header", `text-bg-${(type === 'valid' ? 'green' : 'red')}`);

		let strong = document.createElement("strong");
		strong.classList.add("me-auto");
		strong.innerHTML = title;

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
		toastBody.innerHTML = message;

		toast.appendChild(toastBody);

		this.container.appendChild(toast);
		(bootstrap.Toast.getOrCreateInstance(toast)).show();
	};
}