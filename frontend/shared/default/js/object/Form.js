import Button from "./Button.js";

export default class Form {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;

		this.id = this.element.id || false;
		this.method = this.element.method || 'POST';
		this.action = this.element.action || false;
		this.autocomplete = this.element.autocomplete || false;
		this.prefill = this.element.dataset.prefill || false;
		this.afterSubmit = this.element.dataset.afterSubmit || false;
		this.lockedValue = this.element.dataset.lockedValue || false;
		this.noReserAfterSubmit = this.element.dataset.noReserAfterSubmit || false;

		this.defaultStates = {};
		this.buttons = {};
		if (this.method !== 'GET' || this.method !== 'POST') this.method = 'POST';

		this.init();
	}

	static ScanAndCreate() {
		$("form").each((ids, el) => {
			Form.INSTANCES[el.id] = new Form(el);
		});
	}

	init = () => {
		this.checkDefaultStates();
		this.disableAutocomplete();
		this.disableValidation();
		this.setRequireds();
		this.attachDefaultEvents();
	};

	checkDefaultStates = () => {
		$(this.element).find(":input").each((id, el) => {
			if (!el.id) return;

			this.defaultStates[el.id] = {
				readonly: el.hasAttribute("readonly"),
				disabled: el.hasAttribute("disabled")
			};
		});
	};

	disableAutocomplete = () => {
		if (this.autocomplete !== false) this.element.autocomplete = "off";
	};

	disableValidation = () => {
		this.element.setAttribute("novalidate", "");
	};

	setRequireds = () => {
		$(this.element).find("[required], select[required]").each((idx, el) => {
			$(this.element).find(`[for='${el.id}'], [for='${el.id}-ts-control']`).addClass("required");
		});
	};

	attachDefaultEvents = () => {
		this.attachEvent("submit", (e) => {
			e.preventDefault();
			e.stopPropagation();

			this.submit();
		});
	};

	attachEvent = (on, cb) => {
		this.element.addEventListener(on, cb);
	};

	attachButton = (button) => {
		if (!button instanceof Button) return;
	};
}