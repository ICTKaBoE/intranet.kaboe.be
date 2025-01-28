import Button from "./Button.js";
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
		this.actionField = this.element.dataset.actionField || false;

		this.defaultStates = {};
		this.buttons = {};
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
		if (!id.startsWith("frm")) id = `frm${id}`;
		return Form.INSTANCES[id] || false;
	};

	init = () => {
		// this.createActionField();
		this.checkDefaultStates();
		this.disableAutocomplete();
		this.disableValidation();
		this.setRequireds();
		this.createSteps();
		this.attachDefaultEvents();

		if (this.prefillId) this.prefillForm(this.prefillId);
		else if (this.prefill) this.prefillForm();
	};

	createActionField = () => {
		if (!this.actionField) return;

		let actionField = document.createElement("input");
		actionField.type = "hidden";
		actionField.name = this.actionField;
		actionField.id = this.actionField;

		this.element.appendChild(actionField);
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

	createSteps = () => {
		let steps = $(this.element).find("div[data-step]");
		if (steps.length == 0) return;

		let stepsContainer = $(this.element).find(
			"div[role='form-steps-controller']"
		)[0];
		if (!stepsContainer.classList.contains("row"))
			stepsContainer.classList.add("row");
		if (!stepsContainer.classList.contains("ms-auto"))
			stepsContainer.classList.add("me-auto");

		// PREV BUTTON
		let buttonPrevStepContainer = document.createElement("div");
		buttonPrevStepContainer.classList.add("col-auto");

		let buttonPrevStep = document.createElement("button");
		buttonPrevStep.type = "button";
		buttonPrevStep.onclick = () => {
			this.setActiveStep(this.activeStep - 1);
			this.submit(true);
		};
		buttonPrevStep.classList.add(
			"btn",
			"btn-icon",
			"btn-secondary",
			"d-inline-block"
		);

		let buttonPrevStepIcon = document.createElement("i");
		buttonPrevStepIcon.classList.add("icon", "ti", "ti-chevron-left");

		buttonPrevStep.appendChild(buttonPrevStepIcon);
		buttonPrevStepContainer.appendChild(buttonPrevStep);

		stepsContainer.appendChild(buttonPrevStepContainer);

		// STEP BUTTONS
		let stepsSubContainer = document.createElement("div");
		stepsSubContainer.classList.add("col");

		let stepsCounter = document.createElement("ul");
		stepsCounter.classList.add("steps", "steps-green", "steps-counter");

		for (let i = 0; i < steps.length; i++) {
			let step = document.createElement("li");
			step.classList.add("step-item");
			step.dataset.counter = i + 1;
			step.onclick = () => {
				this.setActiveStep(step.dataset.counter);
				this.submit(true);
			};
			if (steps[i].hasAttribute("data-step-title"))
				step.innerHTML = steps[i].dataset.stepTitle;
			stepsCounter.appendChild(step);
		}

		stepsSubContainer.appendChild(stepsCounter);
		stepsContainer.appendChild(stepsSubContainer);

		// NEXT BUTTON
		let buttonNextStepContainer = document.createElement("div");
		buttonNextStepContainer.classList.add("col-auto");

		let buttonNextStep = document.createElement("button");
		buttonNextStep.type = "button";
		buttonNextStep.onclick = () => {
			this.setActiveStep(this.activeStep + 1);
			this.submit(true);
		};
		buttonNextStep.classList.add(
			"btn",
			"btn-icon",
			"btn-secondary",
			"d-inline-block"
		);

		let buttonNextStepIcon = document.createElement("i");
		buttonNextStepIcon.classList.add("icon", "ti", "ti-chevron-right");

		buttonNextStep.appendChild(buttonNextStepIcon);
		buttonNextStepContainer.appendChild(buttonNextStep);

		stepsContainer.appendChild(buttonNextStepContainer);
		this.setActiveStep(1);
	};

	setActiveStep = (counter) => {
		this.activeStep =
			counter <= 1
				? 1
				: counter >= $(this.element).find("div[data-step]").length
				? $(this.element).find("div[data-step]").length
				: counter;

		if ($(this.element).find("div[data-step]").length == 0) return;

		$("div[data-step]")
			.filter(`div[data-step="${this.activeStep}"]`)
			.removeClass("d-none");
		$("div[data-step]")
			.filter(`div[data-step!="${this.activeStep}"]`)
			.addClass("d-none");

		$("li.step-item")
			.filter(`li[data-counter="${this.activeStep}"]`)
			.addClass("active");
		$("li.step-item")
			.filter(`li[data-counter!="${this.activeStep}"]`)
			.removeClass("active");
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

	reset = () => {
		this.element.reset();
		this.lastLoadedId = null;
		this.resetValidation();
		this.setActiveStep(0);

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

	getSubmitData = () => {
		let data = {};

		$(this.element)
			.find(":input,[role]")
			.each((id, el) => {
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
					console.log(Checkbox.GetInstance(el.id).getValue());
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

	submit = (stepCheck = false) => {
		let data = new FormData();
		let submitData = this.getSubmitData();

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
			if (returnData.statusCode === 500)
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
			if (data.returnToStep) this.setActiveStep(this.activeStep - 1);
			if (data.resetForm) this.reset();
			if (data.setId) this.prefillForm(data.setId);

			if (this.afterSubmit) {
				window[this.afterSubmit]();
			}

			if (!stepCheck || data.returnToStep) {
				setTimeout(() => {
					this.enable();
				}, 500);
			}
		};

		let url = new URL(
			this.action + (this.lastLoadedId ? `/${this.lastLoadedId}` : "")
		);
		if (stepCheck) url.searchParams.set("stepCheck", null);
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

		$(this.element).find("[data-feedback-input]").html("");
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
					.find(`[data-feedback-input='${input}']`)
					.html(validation.feedback);
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
		else if (field.length === 1) {
			field = field[0];

			switch (field.type) {
				case "checkbox":
					String(value) == "true" ||
					String(value) == "on" ||
					String(value) == "1"
						? field.setAttribute("checked", "")
						: field.removeAttribute("checked");
					break;

				default:
					this.setFieldValue(field, value);
					break;
			}
		} else this.setCheckedField(field, value);
	};

	setFieldValue = (field, value) => {
		switch (field.role) {
			case "select":
				Select.INSTANCES[field.id].setValue(value);
				break;

			case "datepicker":
				DatePicker.INSTANCES[field.id].setDate(value);
				break;

			case "tinymce":
				TinyMCE.INSTANCES[field.id].setValue(value);
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
				field.value = value;
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
		if (this.actionField)
			document.getElementById(this.actionField).value = type;

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
}
