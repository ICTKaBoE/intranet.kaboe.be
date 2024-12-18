import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Component from "../../../../shared/default/js/object/Component.js";

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

Component.addActionButton(btnAdd);
