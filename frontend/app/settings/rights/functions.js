import Table from "../../../shared/default/js/object/Table.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Form from "../../../shared/default/js/object/Form.js";

window.loadTable = (value) => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('moduleId', value);
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.edit = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length === 0 || values.includes("-")) {
		alert("Gelieve 1 lijn te selecteren!");
		return;
	}

	Form.GetInstance(`frm${pageId}`).reset();
	Form.GetInstance(`frm${pageId}`).prefillForm(values);
	Helpers.toggleModal("rights");
};

let btnEdit = new Button(null, {
	type: "icon",
	title: "Bewerken",
	icon: "pencil",
	bgColor: "orange",
	onclick: "edit",
});

Helpers.addFloatingButton(btnEdit);

$(document).ready(() => {
	Table.GetInstance(`tbl${pageId}`).attachButton(btnEdit, "==1");
});
