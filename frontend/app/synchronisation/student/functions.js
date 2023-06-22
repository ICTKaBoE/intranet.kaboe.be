import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Select from "../../../shared/ui/js/custom/objects/Select.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.filter = () => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('school', Select.INSTANCES['school'].getValue());
	Table.INSTANCES[`tbl${pageId}`].addExtraData('class', Select.INSTANCES['class'].getValue());
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.resetPassword = () => {
	Helpers.toggleWait();
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	let random = document.getElementById("random").checked;

	$.post(window.location.href.replace("/app/", "/api/v1.0/") + `/${values}/resetPassword/${random}`).always((returnData) => {
		let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));
		if (data.reload) $.each(Table.INSTANCES, (id, t) => t.reload());

		setTimeout(() => {
			Helpers.toggleWait();
			// Helpers.toggleModal(pageId + "PasswordReset");
		}, 500);
	});
};

window.openFilter = () => {
	Helpers.toggleModal(pageId);
};

window.openPasswordReset = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "") {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	} else {
		Helpers.toggleModal(pageId + "PasswordReset");
	}
};

let btnFilter = new Button({
	type: 'icon-text',
	text: "Filteren",
	icon: "filter",
	backgroundColor: "yellow",
	onclick: "openFilter"
});

let btnResetPassword = new Button({
	type: "icon-text",
	text: "Wachtwoord opnieuw instellen",
	icon: "key",
	backgroundColor: "red",
	onclick: "openPasswordReset"
});

Helpers.addButtonToPageTitle(btnResetPassword);
Helpers.addButtonToPageTitle(btnFilter);

setTimeout(() => {
	filter();
}, 500);