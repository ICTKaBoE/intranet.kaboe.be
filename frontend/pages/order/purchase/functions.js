import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Form from "../../../shared/default/js/object/Form.js";
import Component from "../../../shared/default/js/object/Component.js";

window.emptyFilter = () => {
	Select.GetInstance("status").clear();
	Select.GetInstance("schoolId").clear();

	filter();
};

window.filter = () => {
	Table.GetInstance(pageId).addExtraData(
		"status",
		Select.GetInstance("status").getValue().join(";")
	);

	Table.GetInstance(pageId).addExtraData(
		"schoolId",
		Select.GetInstance("schoolId").getValue().join(";")
	);

	Helpers.closeAllModals();
	Table.GetInstance(pageId).reload();
};

window.edit = () => {
	let selected = Table.GetInstance(pageId).getSelectedRowData();
	Helpers.redirect(`/${selected[0].guid || selected[0].id}`);
};

let btnFilter = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "filter",
		title: "Filteren",
		bgColor: "blue",
		modal: "filter",
	},
});

let btnAdd = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "plus",
		title: "Toevoegen",
		bgColor: "green",
		onclick: () => {
			Helpers.redirect("/add");
		},
	},
});

let btnEdit = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "pencil",
		title: "Bewerken",
		bgColor: "orange",
		onclick: "edit",
	},
});

let btnDelete = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "trash",
		title: "Verwijderen",
		bgColor: "red",
		modal: "delete",
		onclick: () => {
			Form.GetInstance(`${pageId}Delete`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid)
					.join(";")
			);
		},
	},
});

Component.addActionButton(btnFilter, btnAdd, btnEdit, btnDelete);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnDelete, ">0");
});
