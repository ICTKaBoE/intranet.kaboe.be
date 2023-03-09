import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.addUserHomeWorkDistance = () => {
	Helpers.redirect("/add");
};

window.editUserHomeWorkDistance = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length === 0 || values.includes("-")) {
		alert("Gelieve 1 afstand te selecteren!");
		return;
	}

	Helpers.redirect(`/edit/${values[0]}`);
};

window.deleteUserHomeWorkDistance = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	if (values.length === 0) {
		alert("Gelieve 1 of meerdere afstanden te selecteren!");
		return;
	}

	Helpers.redirect(`/delete/${values.join("-")}`);
};

let btnAdd = new Button({
	type: "icon-text",
	text: "Toevoegen",
	icon: "plus",
	backgroundColor: "green",
	onclick: "addUserHomeWorkDistance"
});

let btnEdit = new Button({
	type: 'icon-text',
	text: 'Bewerken',
	icon: 'pencil',
	backgroundColor: 'orange',
	onclick: "editUserHomeWorkDistance"
});

let btnDelete = new Button({
	type: 'icon-text',
	text: 'Verwijderen',
	icon: 'trash',
	backgroundColor: 'red',
	onclick: "deleteUserHomeWorkDistance"
});

Helpers.addButtonToPageTitle(btnAdd);
Helpers.addButtonToPageTitle(btnEdit);
Helpers.addButtonToPageTitle(btnDelete);