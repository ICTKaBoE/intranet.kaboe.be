import Button from "../../../../../shared/default/js/object/Button.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import Select from "../../../../../shared/default/js/object/Select.js";

window.assignedToView = (info) => {
	Select.GetInstance("assignedToId").setExtraLoadParam(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
	);
	Select.GetInstance("assignedToId").setDetails(
		Select.GetInstance("assignedTo").getValue()
	);
};

window.edit = () => {
	Select.GetInstance("mediaId").setExtraLoadParam(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
	);
	Select.GetInstance("mediaId").reload();

	Form.GetInstance(`${pageId}Item`).prefillForm(
		Table.GetInstance(`${pageId}Item`)
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
		onclick: () => {
			Form.GetInstance(`${pageId}Item`).reset();
			Select.GetInstance("mediaId").setExtraLoadParam(
				"schoolId",
				Select.GetInstance("schoolId").getValue()
			);
			Select.GetInstance("mediaId").reload();
		},
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

let btnUp = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "arrow-up",
		title: "Omhoog",
		bgColor: "primary",
		onclick: () => {
			Form.GetInstance(`${pageId}ItemUp`).setLastLoadedId(
				Table.GetInstance(`${pageId}Item`)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
			Form.GetInstance(`${pageId}ItemUp`).submit();
		},
	},
});

let btnDown = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "arrow-down",
		title: "Omlaag",
		bgColor: "secondary",
		onclick: () => {
			Form.GetInstance(`${pageId}ItemDown`).setLastLoadedId(
				Table.GetInstance(`${pageId}Item`)
					.getSelectedRowData()
					.map((r) => r.guid ?? r.id)
					.join("_")
			);
			Form.GetInstance(`${pageId}ItemDown`).submit();
		},
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
			Form.GetInstance(`${pageId}Delete`).setLastLoadedId(
				Table.GetInstance(`${pageId}Item`)
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
	Component.addActionButton(
		btnCancel,
		btnSave,
		btnAdd,
		btnEdit,
		btnDelete,
		btnUp,
		btnDown
	);
	$(document).ready(() => {
		Table.GetInstance(pageId + "Item").attachButton(btnEdit, "==1");
		Table.GetInstance(pageId + "Item").attachButton(btnDelete, ">0");
		Table.GetInstance(pageId + "Item").attachButton(btnUp, ">0");
		Table.GetInstance(pageId + "Item").attachButton(btnDown, ">0");
	});
}
