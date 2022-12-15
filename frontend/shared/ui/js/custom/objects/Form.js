import Helpers from "./Helpers.js";
import Select from "./Select.js";
import DatePicker from "./DatePicker.js";

export default class Form {
	static INSTANCES = {};

	constructor(element) {
		this.form = element;
		this.id = this.form.id || false;
		this.method = this.form.method || 'POST';
		this.action = this.form.action || false;
		this.autocomplete = this.form.autocomplete || false;
		this.prefill = this.form.dataset.prefill || false;
		this.afterSubmit = this.form.dataset.afterSubmit || false;

		if (this.method !== 'GET' || this.method !== 'POST') this.method = 'POST';

		this.init();
	}

	static ScanAndCreate() {
		$("form").each((ids, el) => {
			Form.INSTANCES[el.id] = new Form(el);
		});
	}

	init = () => {
		this.disableAutocomplete();
		this.disableValidation();
		this.setRequireds();
		this.attachDefaultEvents();
		this.prefillForm();
	};

	disableAutocomplete = () => {
		if (this.autocomplete !== false) this.form.autocomplete = "off";
	};

	disableValidation = () => {
		this.form.setAttribute("novalidate", "");
	};

	setRequireds = () => {
		$(this.form).find("[required], select[required]").each((idx, el) => {
			$(this.form).find(`[for='${el.id}'], [for='${el.id}-ts-control']`).addClass("required");
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
		this.form.addEventListener(on, cb);
	};

	submit = () => {
		let data = new FormData(this.form);
		this.disable();
		Helpers.toggleWait();

		return $.ajax({
			url: this.action,
			method: this.method,
			data: data,
			cache: false,
			processData: false,
			contentType: false
		}).done(returnData => {
			this.resetAfterSubmit();
		}).fail(returnData => {
			if (returnData.statusCode === 500) alert("Er is een fout gebeurd bij het laden van het formulier!");
		}).always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.validation) this.processValidation(data.validation);
			if (data.message) this.processMessage(data.message);
			if (data.download) this.precessDownload(data.download);
			if (data.reload) window.location.reload();
			if (data.redirect) window.location.href = data.redirect;
			if (data.reset) this.form.reset();
			if (data.toggleModal) Helpers.toggleModal(data.toggleModal);

			if (this.afterSubmit) {
				window[this.afterSubmit]();
			}

			setTimeout(() => {
				this.enable();
				Helpers.toggleWait();
			}, 500);
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
		$(this.form).find(":input").removeClass("is-valid").removeClass("is-invalid");
	};

	processValidation = (data) => {
		$.each(data, (input, validation) => {
			$(this.form)
				.find(`[name='${input}']`)
				.addClass(`is-${validation.state}`);
			$(this.form)
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

		$.get(this.prefill).done(data => {
			if (data.fields) {
				$.each(data.fields, (key, value) => {
					this.setField(key, value);
				});
			}
		}
		).fail(() => {
			alert("Er is een fout gebeurd tijdens het invullen van het formulier!");
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
			case 'select':
				Select.INSTANCES[field.id].setValue(value);
				break;

			case 'datepicker':
				DatePicker.INSTANCES[field.id].setDate(value);
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
		$(this.form).find(":input").prop("disabled", true);
	};

	enable = () => {
		$(this.form).find(":input").prop("disabled", false);
	};

	reset = () => {
		this.resetValidation();
		this.form.reset();
	};
}