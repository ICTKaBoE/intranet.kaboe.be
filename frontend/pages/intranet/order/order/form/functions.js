import Button from "../../../../../shared/default/js/object/Button.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import Select from "../../../../../shared/default/js/object/Select.js";

window.renderOptgroupItem = (data, escape) => {
	return (
		"<div>" +
		(data.optgroupName ? `${data.optgroupName} - ` : "") +
		`${data.name}</div>`
	);
};

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

window.edit = () => {
	Form.GetInstance(`${pageId}Line`).prefillForm(
		Table.GetInstance(`${pageId}Line`)
			.getSelectedRowData()
			.map((r) => r.guid ?? r.id)
			.join("_")
	);
};

let btnAdd = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "plus",
		title: "Toevoegen",
		bgColor: "green",
		modal: "add",
	},
});

let btnEdit = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "pencil",
		title: "Bewerken",
		bgColor: "orange",
		modal: "add",
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
			Form.GetInstance(`${pageId}LineDelete`).setLastLoadedId(
				Table.GetInstance(`${pageId}Line`)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
		},
	},
});

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

if (add == "") {
	Component.addActionButton(btnCancel, btnSave, btnAdd, btnEdit, btnDelete);

	$(document).ready(() => {
		Table.GetInstance(pageId + "Line").attachButton(btnEdit, "==1");
		Table.GetInstance(pageId + "Line").attachButton(btnDelete, ">0");
	});
}
