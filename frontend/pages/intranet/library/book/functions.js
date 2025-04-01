import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

window.emptyFilter = () => {
	Select.GetInstance("schoolId").clear();

	filter();
};

window.filter = () => {
	Table.GetInstance(pageId).addExtraData(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
	);

	Helpers.closeAllModals();
	Table.GetInstance(pageId).reload();
};

window.edit = () => {
	let selected = Table.GetInstance(pageId).getSelectedRowData();
	Helpers.redirect(`/${selected[0].guid || selected[0].id}`);
};

window.setLender = () => {
	let selected = Select.GetInstance("lenderType").getValue();
	Select.GetInstance("lenderInformatId").setDetails(selected);
};

window.setReturner = () => {
	let selected = Select.GetInstance("returnerType").getValue();
	Select.GetInstance("returnerInformatId").setDetails(selected);
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
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
		},
	},
});

let btnLend = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "arrow-forward",
		title: "Uitlenen",
		bgColor: "green",
		modal: "lend",
		onclick: () => {
			Form.GetInstance(`${pageId}Lend`).reset();
			Form.GetInstance(`${pageId}Lend`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
		},
	},
});

let btnReturn = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "arrow-back-up",
		title: "Terugbrengen",
		bgColor: "orange",
		modal: "return",
		onclick: () => {
			Form.GetInstance(`${pageId}Return`).reset();
			Form.GetInstance(`${pageId}Return`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
		},
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
					.map((r) => r.guid ?? r.id)
					.join("_")
			);

			Table.GetInstance(`${pageId}History`).reload();
		},
	},
});

Component.addActionButton(
	btnFilter,
	btnAdd,
	btnEdit,
	btnDelete,
	btnLend,
	btnReturn,
	btnHistory
);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnDelete, ">0");
	Table.GetInstance(pageId).attachButton(btnLend, ">0");
	Table.GetInstance(pageId).attachButton(btnReturn, ">0");
	Table.GetInstance(pageId).attachButton(btnHistory, "==1");
});
