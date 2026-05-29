import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

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
		onclick: "delete",
	},
});

let btnHistory = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "history",
		title: "Geschiedenis",
		bgColor: "primary",
		modal: "history",
		onclick: () => {
			Table.GetInstance(`${pageId}History`).appendSource(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);

			Table.GetInstance(`${pageId}History`).reload();
		},
	},
});

Component.addActionButton(btnFilter, btnAdd, btnEdit, btnDelete, btnHistory);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnDelete, ">0");
	Table.GetInstance(pageId).attachButton(btnHistory, "==1");
});
