import Button from "./Button.js";
import Helpers from "./Helpers.js";

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

		this.buttons[button.id] = button;
	};

	disable = () => {
		$(this.element).find(":input").each((idx, el) => {
			if (!el.id) return;

			// if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.disable();
			el.setAttribute("disabled", "");
		});
	};

	enable = () => {
		$(this.element).find(":input").each((idx, el) => {
			if (!el.id) return;
			if (this.defaultStates[el.id]?.disabled === true) return;

			// if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.enable();
			el.removeAttribute("disabled");
		});
	};

	submit = () => {
		let data = new FormData(this.element);

		this.disable();

		let done = (returnData) => {
			if (!this.noReserAfterSubmit) this.resetAfterSubmit();
		};

		let fail = (returnData) => {
			if (returnData.statusCode === 500) alert("Er is een fout gebeurd bij het indienen van het formulier!");
		};

		let always = (returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.validation) this.processValidation(data.validation);
			if (data.redirect) Helpers.redirect(data.redirect);

			if (this.afterSubmit) {
				window[this.afterSubmit]();
			}

			setTimeout(() => {
				this.enable();
			}, 500);
		};

		return Helpers.request({
			url: this.action,
			method: this.method,
			data: data,
			done: done,
			fail: fail,
			always: always
		});
	};

	resetAfterSubmit = () => {
	};

	resetValidation = () => {
		$(this.element).find(":input").removeClass("is-valid").removeClass("is-invalid");
	};

	processValidation = (data) => {
		this.resetValidation();

		$.each(data, (input, validation) => {
			$(this.element)
				.find(`[name='${input}']`)
				.addClass(`is-${validation.state}`);
			$(this.element)
				.find(`[data-feedback-input='${input}']`)
				.html(validation.feedback);
		});
	};
}