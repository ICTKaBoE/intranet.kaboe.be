import Helpers from "./Helpers.js";
import Select from "./Select.js";
import DatePicker from "./DatePicker.js";
import TinyMCE from "./TinyMCE.js";

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
		this.prefillForm();
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

	getSubmitData = () => {
		let data = {};

		$(this.element).find(":input").each((id, el) => {
			if (null !== el.name) {
				if (el.role === "select") {
					let v = Select.INSTANCES[el.id].getValue();
					data[el.id] = (typeof v == "string" ? v : v.join(";"));
				} else data[el.id] = el.value;
			}
		});

		return data;
	};

	submit = () => {
		let data = new FormData;
		let submitData = this.getSubmitData();
		console.log(submitData);

		Object.keys(submitData).forEach(k => {
			data.append(k, submitData[k]);
		});

		this.disable();
		Helpers.toggleWait();

		let done = (returnData) => {
			if (!this.noReserAfterSubmit) this.resetAfterSubmit();
		};

		let fail = (returnData) => {
			if (returnData.statusCode === 500) alert("Er is een fout gebeurd bij het laden van het formulier!");
		};

		let always = (returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.validation) this.processValidation(data.validation);
			if (data.message) this.processMessage(data.message);
			if (data.download) this.precessDownload(data.download);
			if (data.reload) window.location.reload();
			if (data.redirect) window.location.href = data.redirect;
			if (data.reset) this.element.reset();
			if (data.toggleModal) Helpers.toggleModal(data.toggleModal);

			if (this.afterSubmit) {
				window[this.afterSubmit]();
			}

			setTimeout(() => {
				Helpers.toggleWait();
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

	processMessage = (message) => {
		let id = "";
		if (message.state == 'error') id = "pageError";
		else if (message.state == 'warning') id = "pageWarning";
		else id = "pageSuccess";

		// toggleModal(id, message.content, message.disappear || false);
	};

	resetAfterSubmit = () => {
		this.resetValidation();
	};

	resetValidation = () => {
		$(this.element).find(":input").removeClass("is-valid").removeClass("is-invalid");
	};

	processValidation = (data) => {
		$.each(data, (input, validation) => {
			$(this.element)
				.find(`[name='${input}']`)
				.addClass(`is-${validation.state}`);
			$(this.element)
				.find(`[data-feedback-input='${input}']`)
				.html(validation.feedback);
		});
	};

	precessDownload = (link) => {
		let a = document.createElement('a');
		a.type = "download";
		a.href = link;
		a.click();

		a = null;
	};

	prefillForm = () => {
		if (!this.prefill) return;

		let done = (data) => {
			if (data.fields) {
				let fields = Helpers.flattenObject(data.fields);
				$.each(fields, (key, value) => {
					this.setField(key, value);
				});

				if (this.lockedValue) {
					if (data.fields[this.lockedValue] === true) this.disable();
				}
			}

			if (data.validation) this.processValidation(data.validation);
		};

		let fail = () => {
			alert("Er is een fout gebeurd tijdens het invullen van het formulier!");
		};

		Helpers.request({
			url: this.prefill,
			method: 'GET',
			done: done,
			fail: fail
		});
	};

	setField = (name, value) => {
		let field = $(`[name='${name}']`);

		if (field === undefined || field.length === 0) return;
		else if (field.length === 1) {
			field = field[0];

			switch (field.type) {
				case 'checkbox': (String(value) == "true" || String(value) == "on") ? field.setAttribute("checked", "") : field.removeAttribute("checked");
					break;

				default: this.setFieldValue(field, value);
					break;
			}

		} else this.setCheckedField(field, value);
	};

	setFieldValue = (field, value) => {
		switch (field.role) {
			case 'select': {
				Select.INSTANCES[field.id].defaultValue = value;
				Select.INSTANCES[field.id].setValue(value);
			}
				break;

			case 'datepicker':
				DatePicker.INSTANCES[field.id].setDate(value);
				break;

			case 'tinymce':
				TinyMCE.INSTANCES[field.id].setValue(value);
				break;

			default: field.value = value;
				break;
		}
	};

	setCheckedField = (fields, value) => {
		$.each(fields, (idx, field) => {
			if (field.value === value) {
				field.setAttribute("checked", "");
			} else {
				field.removeAttribute("checked");
			}
		});
	};

	disable = () => {
		$(this.element).find(":input").each((idx, el) => {
			if (!el.id) return;

			if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.disable();
			else el.setAttribute("disabled", "");
		});
	};

	enable = () => {
		$(this.element).find(":input").each((idx, el) => {
			if (!el.id) return;
			if (this.defaultStates[el.id]?.disabled === true) return;

			if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.enable();
			else el.removeAttribute("disabled");
		});
	};

	reset = () => {
		this.resetValidation();
		this.element.reset();
	};
}