import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
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
		onclick: () => {
			Form.GetInstance(`${pageId}Delete`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
		},
	},
});

let btnRequestQuote = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "pointer-question",
		title: "Offerte aanvragen",
		bgColor: "primary",
		modal: "requestQuote",
		onclick: () => {
			Form.GetInstance(`${pageId}RequestQuote`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
		},
	},
});

let btnRequestAccept = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "basket-question",
		title: "Goedkeuring aanvragen",
		bgColor: "orange",
		modal: "requestAccept",
		onclick: () => {
			Form.GetInstance(`${pageId}RequestAccept`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
		},
	},
});

let btnOrder = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "basket",
		title: "Bestelling plaatsen",
		bgColor: "green",
		modal: "order",
		onclick: () => {
			Form.GetInstance(`${pageId}Order`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
		},
	},
});

Component.addActionButton(
	btnFilter,
	btnAdd,
	btnEdit,
	btnDelete,
	btnRequestQuote,
	btnRequestAccept,
	btnOrder
);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnDelete, ">0");
	Table.GetInstance(pageId).attachButton(btnRequestQuote, ">0");
	Table.GetInstance(pageId).attachButton(btnRequestAccept, ">0");
	Table.GetInstance(pageId).attachButton(btnOrder, ">0");
});
