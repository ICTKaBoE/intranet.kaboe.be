import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Form from "../../../../shared/default/js/object/Form.js";

window.emptyFilter = () => {
	Select.GetInstance("status").clear();
	Select.GetInstance("schoolId").clear();

	filter();
};

window.filter = () => {
	Table.GetInstance(pageId).addExtraData(
		"status",
		Select.GetInstance("status").getValue()
	);

	Table.GetInstance(pageId).addExtraData(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
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

let btnPrint = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "printer",
		title: "Print inschrijvingsdocumenten",
		bgColor: "orange",
		modal: "wait-print",
		onclick: () => {
			Form.GetInstance(`${pageId}Print`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
			Form.GetInstance(`${pageId}Print`).submit();
		},
	},
});

Component.addActionButton(btnFilter, btnAdd, btnPrint);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnPrint, ">0");
});
