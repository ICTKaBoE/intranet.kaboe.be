import Table from "../../../shared/ui/js/custom/objects/Table.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Select from "../../../shared/ui/js/custom/objects/Select.js";

window.loadTable = (value) => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('module', value);
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.addRights = () => {
	Helpers.redirect(`/add/${Select.INSTANCES[`sel${pageId}`].getValue()}`);
};

window.editRights = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "" || values.includes("-")) {
		alert("Gelieve 1 lijn te selecteren!");
		return;
	} else {
		Helpers.redirect(`/edit/${values}`);
	}
};

window.removeRights = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "") {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	} else {
		Helpers.toggleWait();

		$.post(window.location.href.replace("/app/", "/api/v1.0/app/") + `/delete/${values}`).always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));
			if (data.reload) $.each(Table.INSTANCES, (id, t) => t.reload());

			setTimeout(() => {
				Helpers.toggleWait();
			}, 500);
		});
	}
};

let btnAdd = new Button({
	type: 'icon-text',
	text: "Toevoegen",
	icon: "plus",
	backgroundColor: "green",
	onclick: "addRights"
});

let btnEdit = new Button({
	type: 'icon-text',
	text: 'Bewerken',
	icon: 'pencil',
	backgroundColor: 'orange',
	onclick: "editRights"
});

let btnDelete = new Button({
	type: 'icon-text',
	text: "Rechten intrekken",
	icon: "trash",
	backgroundColor: "red",
	onclick: "removeRights"
});

Helpers.addButtonToPageTitle(btnAdd);
Helpers.addButtonToPageTitle(btnEdit);
Helpers.addButtonToPageTitle(btnDelete);