import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";
import Form from "../../../shared/default/js/object/Form.js";

window.changeLocationType = () => {
	let type = Select.GetInstance("type").getValue();
	Select.GetInstance("startId").setDetails(type);
};

window.emptyFilter = () => {
	Select.GetInstance("type").clear();
	Select.GetInstance("startId").clear();

	filter();
};

window.filter = () => {
	Table.GetInstance(pageId).addExtraData(
		"type",
		Select.GetInstance("type").getValue()
	);
	Table.GetInstance(pageId).addExtraData(
		"startId",
		Select.GetInstance("startId").getValue()
	);

	Helpers.closeAllModals();
	Table.GetInstance(pageId).reload();
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
		onclick: () => {
			let selected = Table.GetInstance(pageId).getSelectedRowData();
			Helpers.redirect(`/${selected[0].guid || selected[0].id}`);
		},
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
			Form.GetInstance(pageId).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid)
					.join(";")
			);
		},
	},
});

Helpers.addActionButton(btnFilter, btnAdd, btnEdit, btnDelete);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnDelete, ">0");
});
