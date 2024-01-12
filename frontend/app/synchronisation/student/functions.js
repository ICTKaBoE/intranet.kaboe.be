import Form from "../../../shared/default/js/object/Form.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";

window.filter = () => {
	Helpers.toggleModal("filter");
};

window.applyFilter = () => {
	let _school = Select.INSTANCES["filterSchool"].getValue();
	let _class = Select.INSTANCES["filterClass"].getValue();

	Table.GetInstance(pageId).addExtraData("school", _school);
	Table.GetInstance(pageId).addExtraData("class", _class);
	Table.GetInstance(pageId).reload();
	Helpers.toggleModal("filter");
};

window.resetPassword = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length === 0) {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	}

	Form.GetInstance(`${pageId}Reset`).prefillForm(values);
	Helpers.toggleModal("reset");
};

let btnFilter = new Button(null, {
	type: "icon",
	title: "Filteren",
	icon: "filter",
	bgColor: "primary",
	onclick: "filter",
});

let btnResetPassword = new Button(null, {
	type: "icon",
	title: "Wachtwoord resetten",
	icon: "key",
	bgColor: "danger",
	onclick: "resetPassword",
});

Helpers.addFloatingButton(btnFilter, btnResetPassword);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnResetPassword, ">0");
});
