import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Form from "../../../../shared/default/js/object/Form.js";

window.emptyFilter = () => {
	// Select.GetInstance("status").clear();
	Select.GetInstance("schoolId").clear();

	filter();
};

window.filter = () => {
	// Table.GetInstance(pageId).addExtraData(
	// 	"status",
	// 	Select.GetInstance("status").getValue()
	// );

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

let btnFilter = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "filter",
		title: "Filteren",
		bgColor: "blue",
		modal: "filter",
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

let btnPrint = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "printer",
		title: "Print Aangifteformulier",
		bgColor: "primary",
		modal: "print",
		onclick: () => {
			Form.GetInstance(`${pageId}Print`).setLastLoadedId(
				Table.GetInstance(pageId)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
		},
	},
});

Component.addActionButton(btnFilter, btnEdit, btnPrint);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnEdit, "==1");
	Table.GetInstance(pageId).attachButton(btnPrint, ">0");
});
