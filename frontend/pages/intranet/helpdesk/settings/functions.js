import Button from "../../../../shared/default/js/object/Button.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

Button.GetInstance("btnResetLastNumber").setOnClick(() => {
	document.getElementById("lastNumber").value = 0;
});

let btnSave = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "check",
		text: "Opslaan",
		title: "Opslaan",
		bgColor: "primary",
		onclick: () => {
			Form.GetInstance(pageId).submit();
		},
	},
});

Component.addActionButton(btnSave);
