import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";

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

Component.addActionButton(btnCancel, btnSave);
