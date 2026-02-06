import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
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

let btnView = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "eye",
		title: "Bekijken",
		bgColor: "orange",
		onclick: "view",
	},
});

Component.addActionButton(btnFilter, btnAdd, btnView);

$(document).ready(() => {
	Table.GetInstance(pageId).attachButton(btnView, "==1");
});
