import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";

let btnCancel = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "arrow-left",
		text: "Annuleren",
		title: "Annuleren",
		bgColor: "secondary",
		onclick: () => {
			history.back();
		},
	},
});

let btnDeny = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "x",
		text: "Afkeuren",
		title: "Afkeuren",
		bgColor: "danger",
		onclick: () => {
			Select.GetInstance("status").setValue("D");

			setTimeout(() => {
				Form.GetInstance(pageId).submit();
			}, 500);
		},
	},
});

let btnAccept = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "check",
		text: "Goedkeuren",
		title: "Goedkeuren",
		bgColor: "success",
		onclick: () => {
			Select.GetInstance("status").setValue("A");

			setTimeout(() => {
				Form.GetInstance(pageId).submit();
			}, 500);
		},
	},
});

Component.addActionButton(btnCancel, btnDeny, btnAccept);
