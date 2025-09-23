import Helpers from "./Helpers.js";
import Select from "./Select.js";
import DatePicker from "./DatePicker.js";
import TinyMCE from "./TinyMCE.js";
import Checkbox from "./Checkbox.js";
import ColorInput from "./ColorInput.js";

export default class Form {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;

		this.id = this.element.getAttribute("id") || false;
		this.method = (
			this.element.getAttribute("method") || "POST"
		).toUpperCase();
		this.action = this.element.action || false;
		this.autocomplete = this.element.autocomplete || false;
		this.prefill = this.element.hasAttribute("data-prefill");
		this.prefillId = this.element.dataset.prefillId || false;
		this.source = this.element.dataset.source || this.action;
		this.afterSubmit = this.element.dataset.afterSubmit || false;
		this.lockedValue = this.element.dataset.lockedValue || false;
		this.noReserAfterSubmit =
			this.element.dataset.noReserAfterSubmit || false;

		this.defaultStates = {};
		this.hasSteps = $(this.element).find("div[data-step]").length > 0;
		this.activeStep = 0;
		this.lastLoadedId = null;
		this.locked = false;

		this.init();
	}

	static ScanAndCreate = () => {
		$("form").each((ids, el) => {
			if (!Form.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Form.INSTANCES[el.getAttribute("id")] = new Form(el);
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("frm"))
			id = `frm${
				String(id).charAt(0).toUpperCase() + String(id).slice(1)
			}`;
		return Form.INSTANCES[id] || false;
	};

	init = () => {
		this.checkDefaultStates();
		this.disableAutocomplete();
		this.disableValidation();
		this.setRequireds();
		if (this.hasSteps) this.setActiveStep(1);
		// this.createSteps();
		this.attachDefaultEvents();

		if (this.prefillId) this.prefillForm(this.prefillId);
		else if (this.prefill) this.prefillForm();
	};

	checkDefaultStates = () => {
		$(this.element)
			.find(":input")
			.each((id, el) => {
				if (!el.id) return;

				this.defaultStates[el.id] = {
					readonly: el.hasAttribute("readonly"),
					disabled: el.hasAttribute("disabled"),
					noLock: el.hasAttribute("data-no-lock"),
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
		$(this.element)
			.find("[required], select[required]")
			.each((idx, el) => {
				$(this.element)
					.find(`[for='${el.id}'], [for='${el.id}-ts-control']`)
					.addClass("required");
			});
	};

	setActiveStep = (counter) => {
		this.activeStep =
			counter <= 1
				? 1
				: counter >= this.getSteps()
				? this.getSteps()
				: counter;

		if (this.getSteps() == 0) return;

		$("div[data-step]")
			.filter(`div[data-step="${this.activeStep}"]`)
			.removeClass("d-none");
		$("div[data-step]")
			.filter(`div[data-step!="${this.activeStep}"]`)
			.addClass("d-none");

		$("[role='step-title']").html(
			`${this.getActiveStepObject().dataset.step}/${this.getSteps()}: ${
				this.getActiveStepObject().dataset.title
			}`
		);

		let activeStepObj = this.getActiveStepObject();

		if (activeStepObj.hasAttribute("data-before-load")) {
			window[activeStepObj.dataset.beforeLoad]();
		}
	};

	setPreviousStep = () => {
		this.submit(true);
	};

	setNextStep = () => {
		this.submit(true);
	};

	getStepObjects = () => $(this.element).find("div[data-step]");
	getSteps = () => this.getStepObjects().length;

	getActiveStep = () => this.activeStep;
	getActiveStepObject = () =>
		$("div[data-step]").filter(`div[data-step="${this.activeStep}"]`)[0];
	getPrevStepObject = () =>
		$("div[data-step]").filter(
			`div[data-step="${this.activeStep - 1}"]`
		)[0];
	getNextStepObject = () =>
		$("div[data-step]").filter(
			`div[data-step="${this.activeStep + 1}"]`
		)[0];

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

	reset = () => {
		this.element.reset();
		this.lastLoadedId = null;
		this.resetValidation();
		this.setActiveStep(1);

		$(this.element)
			.find(":input")
			.each((idx, el) => {
				if (!el.id) return;

				if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.clear();
			});
	};

	disable = () => {
		$(this.element)
			.find(":input")
			.each((idx, el) => {
				if (!el.id) return;
				if (this.defaultStates[el.id]?.noLock === true) return;

				if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.disable();
				else if (el.role === "tinymce")
					TinyMCE.INSTANCES[el.id]?.disable();
				else el.disabled = true;
			});
	};

	enable = () => {
		$(this.element)
			.find(":input")
			.each((idx, el) => {
				if (!el.id) return;
				if (this.defaultStates[el.id]?.disabled === true) return;

				if (el.tagName === "SELECT") Select.INSTANCES[el.id]?.enable();
				else if (el.role === "tinymce")
					TinyMCE.INSTANCES[el.id]?.enable();
				else el.disabled = false;
			});
	};

	getSubmitData = (stepCheck = false, stepDirection = "+") => {
		let data = {};
		let elements = $(this.element).find(":input,[role]");
		if (stepCheck) {
			elements = $(this.element)
				.find(`[data-step=${this.activeStep}]`)
				.find(":input,[role]");

			data["_step_"] = this.activeStep;
			data["_stepDirection_"] = stepDirection;
		}

		elements.each((id, el) => {
			if (null === el) return;
			if (el.type === "checkbox" || el.type === "radio") return;

			let name = el.name;
			let value = el.value;

			if (el.role === "select") {
				let v = Select.GetInstance(el.id).getValue();
				data[name] = typeof v == "string" ? v : v.join(";");
			} else if (el.role === "tinymce")
				data[name] = TinyMCE.INSTANCES[el.id].getValue();
			else if (el.role === "checkbox") {
				name = Checkbox.GetInstance(el.id).getName();
				data[name] = Checkbox.GetInstance(el.id).getValue();
			} else if (el.role === "colorinput") {
				name = ColorInput.GetInstance(el.id).getName();
				data[name] = ColorInput.GetInstance(el.id).getValue();
			} else if (el.type === "file") {
				data[name] = [];

				for (let i = 0; i < el.files.length; i++) {
					data[name].push(el.files[i]);
				}
			} else data[name] = value;

			if (!name) delete data[name];
		});

		return data;
	};

	submit = (stepCheck = false, stepDirection = "+") => {
		let data = new FormData();
		let submitData = this.getSubmitData(stepCheck, stepDirection);

		Object.keys(submitData).forEach((k) => {
			if (Array.isArray(submitData[k])) {
				for (let i = 0; i < submitData[k].length; i++)
					data.append(k + "[]", submitData[k][i]);
			} else data.set(k, submitData[k]);
		});

		this.disable();

		let done = (returnData) => {
			if (!this.noReserAfterSubmit) this.resetAfterSubmit();
		};

		let fail = (returnData) => {
			if (returnData.status == 400) this.enable();
			if (returnData.status === 500)
				alert(
					"Er is een fout gebeurd bij het indienen van het formulier!"
				);
		};

		let always = (returnData) => {
			let data = JSON.parse(
				returnData.responseText || JSON.stringify(returnData)
			);

			Helpers.processRequestResponse(data);
			this.processValidation(data.validation);
			if (data.activeStep) this.setActiveStep(data.activeStep);
			if (data.resetForm) this.reset();
			if (data.setId) this.prefillForm(data.setId);

			if (this.afterSubmit) {
				window[this.afterSubmit]();
			}

			if (stepCheck || data.activeStep) {
				setTimeout(() => {
					this.enable();
				}, 500);
			}
		};

		let url = new URL(
			this.action + (this.lastLoadedId ? `/${this.lastLoadedId}` : "")
		);
		if (new URL(window.location.href).searchParams.has("redirect"))
			url.searchParams.set(
				"redirect",
				new URL(window.location.href).searchParams.get("redirect")
			);

		return Helpers.request({
			url: url.toString(),
			method: this.method,
			data: data,
			done: done,
			fail: fail,
			always: always,
		});
	};

	resetAfterSubmit = () => {};

	resetValidation = () => {
		$(this.element)
			.find(".is-valid, .is-invalid")
			.removeClass("is-valid")
			.removeClass("is-invalid");

		$(this.element).find("*.invalid-feedback").remove();
	};

	processValidation = (data) => {
		this.resetValidation();

		$.each(data, (input, validation) => {
			$(this.element)
				.find(`[name='${input}']`)
				.addClass(`is-${validation.state}`);

			$(this.element)
				.find(`[id='${input}-ts-control']`)
				.parent()
				.addClass(`is-${validation.state}`);

			if (validation.feedback)
				$(this.element)
					.find(`[name='${input}']`)
					.parent()
					.append(
						`<p class='invalid-feedback'>${validation.feedback}</p>`
					);
		});
	};

	prefillForm = (id = null) => {
		this.lastLoadedId = id;

		fetch(
			this.source +
				(this.lastLoadedId == null ? "" : `/${this.lastLoadedId}`),
			{
				credentials: "include",
			}
		)
			.then((res) => res.json())
			.then((json) => {
				this.prefillFields(json.fields);
			});
	};

	prefillFields = (fields) => {
		fields = Helpers.flattenObject(fields);

		$.each(fields, (key, value) => {
			this.setField(key, value);
		});

		if (this.lockedValue) {
			this.locked = fields[this.lockedValue];
			if (fields[this.lockedValue] === true) this.disable();
			else this.enable();
		}
	};

	setField = (name, value) => {
		let field = $(`[name='${name}']`);

		if (field === undefined || field.length === 0) return;

		field = field[0];
		switch (field.role || field.type) {
			case "select":
				Select.INSTANCES[field.id].setValue(value);
				break;

			case "datepicker":
				DatePicker.INSTANCES[field.id].setDate(value);
				break;

			case "tinymce":
				TinyMCE.INSTANCES[field.id].setValue(value);
				break;

			case "checkbox":
				Checkbox.GetInstance(field.dataset.id || field.name).setValue(
					value
				);
				break;

			case "file":
				{
					// Create a new File object
					const myFile = new File([""], value, {
						type: "text/plain",
						lastModified: new Date(),
					});

					// Now let's create a FileList
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(myFile);
					field.files = dataTransfer.files;

					// Help Safari out
					if (field.webkitEntries.length) {
						field.dataset.file = `${dataTransfer.files[0].name}`;
					}
				}
				break;

			default:
				{
					field.value = value;
					if (field.hasAttribute("data-mask")) {
						field.focus();
						field.blur();
					}
				}
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

	setActiveType = (type) => {
		$(this.element)
			.find("[data-form-type]")
			.each((id, el) => {
				if (el.dataset.formType.includes(type)) {
					if (el.classList.contains("d-none"))
						el.classList.remove("d-none");
				} else {
					if (!el.classList.contains("d-none"))
						el.classList.add("d-none");
				}
			});
	};

	setLastLoadedId = (id) => {
		this.lastLoadedId = id;
	};

	hide = () => {
		this.element.classList.add("d-none");
	};

	show = () => {
		this.element.classList.remove("d-none");
	};
}
