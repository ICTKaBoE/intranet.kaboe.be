import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.loadTable = (value) => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('schoolId', value);
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.addArticle = () => {
	Helpers.redirect("/add");
};

window.editArticle = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length === 0 || values.includes("-")) {
		alert("Gelieve 1 lijn te selecteren!");
		return;
	}

	Helpers.redirect(`/edit/${values}`);
};

window.deleteArticle = () => {
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
	onclick: "addArticle"
});

let btnEdit = new Button({
	type: 'icon-text',
	text: 'Bewerken',
	icon: 'pencil',
	backgroundColor: 'orange',
	onclick: "editArticle"
});

let btnDelete = new Button({
	type: 'icon-text',
	text: 'Verwijderen',
	icon: 'trash',
	backgroundColor: 'red',
	onclick: "deleteArticle"
});

Helpers.addButtonToPageTitle(btnAdd);
Helpers.addButtonToPageTitle(btnEdit);
Helpers.addButtonToPageTitle(btnDelete);