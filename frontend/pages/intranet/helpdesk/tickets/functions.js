import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";

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

window.view = () => {
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

let btnView = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "eye",
		title: "Bekijken",
		bgColor: "orange",
		onclick: "view",
	},
});

Component.addActionButton(btnFilter, btnView);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnView, "==1");
});
