import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.addPage = () => {
	Helpers.redirect("/add");
};

window.editPage = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length === 0 || values.includes("-")) {
		alert("Gelieve 1 lijn te selecteren!");
		return;
	}

	Helpers.redirect(`/edit/${values}`);
};

window.deletePage = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	if (values.length === 0) {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	}

	Helpers.redirect(`/delete/${values}`);
};

let btnAdd = new Button({
	type: "icon-text",
	text: "Toevoegen",
	icon: "plus",
	backgroundColor: "green",
	onclick: "addPage"
});

let btnEdit = new Button({
	type: 'icon-text',
	text: 'Bewerken',
	icon: 'pencil',
	backgroundColor: 'orange',
	onclick: "editPage"
});

let btnDelete = new Button({
	type: 'icon-text',
	text: 'Verwijderen',
	icon: 'trash',
	backgroundColor: 'red',
	onclick: "deletePage"
});

Helpers.addButtonToPageTitle(btnAdd);
Helpers.addButtonToPageTitle(btnEdit);
Helpers.addButtonToPageTitle(btnDelete);