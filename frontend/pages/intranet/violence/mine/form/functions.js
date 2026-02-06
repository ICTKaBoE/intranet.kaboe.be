import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.anonymousView = (info) => {
	if (Boolean(Checkbox.GetInstance("Anonymous").getValue()))
		Select.GetInstance("victimId").disable();
	else Select.GetInstance("victimId").enable();
};

window.formView = (info) => {
	let val = Select.GetInstance("form").getValue();
	val = val.split(";").map((i) => parseInt(i));

	if (val.includes(window.SELECT_OTHER_ID))
		document.getElementById("form-O").classList.remove("d-none");
	else document.getElementById("form-O").classList.add("d-none");
};

window.outView = (info) => {
	let val = Select.GetInstance("out").getValue();
	val = val.split(";").map((i) => parseInt(i));

	if (val.includes(window.SELECT_OTHER_ID))
		document.getElementById("out-O").classList.remove("d-none");
	else document.getElementById("out-O").classList.add("d-none");
};

window.intentionView = (info) => {
	let val = Select.GetInstance("intention").getValue();
	val = val.split(";").map((i) => parseInt(i));

	if (val.includes(window.SELECT_OTHER_ID))
		document.getElementById("intention-O").classList.remove("d-none");
	else document.getElementById("intention-O").classList.add("d-none");
};

window.causeView = (info) => {
	let val = Select.GetInstance("cause").getValue();
	val = val.split(";").map((i) => parseInt(i));

	if (val.includes(window.SELECT_OTHER_ID))
		document.getElementById("cause-O").classList.remove("d-none");
	else document.getElementById("cause-O").classList.add("d-none");
};

$(document).ready(() => {
	window.anonymousView();

	Button.GetInstance("PrevStep").setOnClick(() => {
		Form.GetInstance(pageId).submit(true, "-");
	});

	Button.GetInstance("NextStep").setOnClick(() => {
		Form.GetInstance(pageId).submit(true, "+");
	});
});
