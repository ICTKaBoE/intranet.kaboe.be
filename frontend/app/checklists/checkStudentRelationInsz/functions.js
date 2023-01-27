import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Select from "../../../shared/ui/js/custom/objects/Select.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.filter = () => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('school', Select.INSTANCES['school'].getValue());
	Table.INSTANCES[`tbl${pageId}`].addExtraData('class', Select.INSTANCES['class'].getValue());
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.filterRelation = () => {
	Helpers.toggleModal(pageId);
};

window.acceptRelation = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "") {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	} else {
		Helpers.toggleWait();

		$.post(window.location.href.replace("/app/", "/api/v1.0/app/") + `/approve/${values}`).always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));
			if (data.reload) $.each(Table.INSTANCES, (id, t) => t.reload());

			setTimeout(() => {
				Helpers.toggleWait();
			}, 500);
		});
	}
};

window.editRelation = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "") {
		alert("Gelieve 1 lijn te selecteren!");
		return;
	}

	Helpers.redirect(`/edit/${values}`);
};

window.prepareForInformat = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values.length == 0) {
		Table.INSTANCES[`tbl${pageId}`].checkAll();
		Table.INSTANCES[`tbl${pageId}`].checkboxCountCheck();
		values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	}

	Helpers.toggleWait();

	$.post(window.location.href.replace("/app/", "/api/v1.0/app/") + `/prepareForInformat/${values}/${Select.INSTANCES['school'].getValue()}/${Select.INSTANCES['class'].getValue()}`).always((returnData) => {
		let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));
		if (data.reload) $.each(Table.INSTANCES, (id, t) => t.reload());
		if (data.download) {
			let a = document.createElement('a');
			a.type = "download";
			a.href = data.download;
			a.click();

			a = null;
		}

		setTimeout(() => {
			Helpers.toggleWait();
		}, 500);
	});
};

let btnFilter = new Button({
	type: 'icon-text',
	text: "Filteren",
	icon: "filter",
	backgroundColor: "yellow",
	onclick: "filterRelation"
});

let btnAccept = new Button({
	type: "icon-text",
	text: "Goedkeuren",
	icon: "thumb-up",
	backgroundColor: "green",
	onclick: "acceptRelation"
});

let btnEdit = new Button({
	type: 'icon-text',
	text: 'Bewerken',
	icon: 'pencil',
	backgroundColor: 'orange',
	onclick: "editRelation"
});

let btnPrepare = new Button({
	type: 'icon-text',
	text: "Klaarmaken voor Informat",
	icon: "send",
	backgroundColor: "green",
	onclick: "prepareForInformat"
});

Helpers.addButtonToPageTitle(btnFilter);
Helpers.addButtonToPageTitle(btnEdit);
Helpers.addButtonToPageTitle(btnAccept);
Helpers.addButtonToPageTitle(btnPrepare);

setTimeout(() => {
	filter();
}, 500);