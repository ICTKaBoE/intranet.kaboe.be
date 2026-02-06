import Button from "../../../../../shared/default/js/object/Button.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";

let btnPresent = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "check",
		text: "Aanwezig",
		title: "Aanwezig",
		bgColor: "green",
		onclick: () => {
			Form.GetInstance(`${pageId}Present`).setLastLoadedId(
				Table.GetInstance(`${pageId}Students`)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
			Form.GetInstance(`${pageId}Present`).submit();
		},
	},
});

let btnNotPresent = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "x",
		text: "Afwezig",
		title: "Afwezig",
		bgColor: "red",
		onclick: () => {
			Form.GetInstance(`${pageId}NotPresent`).setLastLoadedId(
				Table.GetInstance(`${pageId}Students`)
					.getSelectedRowData()
					.map((r) => r.guid || r.id)
					.join("_")
			);
			Form.GetInstance(`${pageId}NotPresent`).submit();
		},
	},
});

Component.addActionButton(btnPresent, btnNotPresent);

$(document).ready(() => {
	Table.GetInstance(`${pageId}Students`).attachButton(btnPresent, ">=1");
	Table.GetInstance(`${pageId}Students`).attachButton(btnNotPresent, ">=1");
});
