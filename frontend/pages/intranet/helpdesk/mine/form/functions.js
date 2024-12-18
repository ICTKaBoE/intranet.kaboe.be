import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";

window.deviceView = (info) => {
	let category = Select.GetInstance("category").getValue();
	category = category.split("-")[0];

	if (category == "O") {
		Select.GetInstance("assetId").disable();
	} else {
		Select.GetInstance("assetId").setExtraLoadParam(
			"schoolId",
			Select.GetInstance("schoolId").getValue()
		);
		Select.GetInstance("assetId").setDetails(category);

		setTimeout(() => {
			if (Form.GetInstance(pageId).locked) {
				Select.GetInstance("assetId").disable();
				Select.GetInstance("roomId").disable();
			}
		}, 100);
	}
};

window.renderOptgroupItem = (data, escape) => {
	return `<div>${data.optgroupName} - ${data.name}</div>`;
};

let btnCancel = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "x",
		text: "Annuleren",
		title: "Annuleren",
		bgColor: "red",
		onclick: () => {
			history.back();
		},
	},
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

if (add == "") Component.addActionButton(btnCancel, btnSave);
