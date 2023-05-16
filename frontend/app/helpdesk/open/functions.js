import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Select from "../../../shared/ui/js/custom/objects/Select.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.showDetails = () => {
	let id = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (id.length === 0 || id.includes("-")) {
		alert("Gelieve 1 ticket te selecteren!");
		return;
	}

	Helpers.redirect(`/../details?id=${id}`);
};

window.filter = () => {
	Table.INSTANCES[`tbl${pageId}`].addExtraData('school', Select.INSTANCES['school'].getValue());
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.openFilter = () => {
	Helpers.toggleModal(pageId);
};

window.clearFilter = () => {
	Table.INSTANCES[`tbl${pageId}`].clearExtraData();
	Table.INSTANCES[`tbl${pageId}`].reload();
};

window.claim = () => {
	let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

	if (values === "") {
		alert("Gelieve 1 of meerdere lijnen te selecteren!");
		return;
	} else {
		Helpers.toggleWait();

		$.post(window.location.href.replace("/app/", "/api/v1.0/app/") + `/claim/${values}`).always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.reload) $.each(Table.INSTANCES, (id, t) => t.reload());

			setTimeout(() => {
				Helpers.toggleWait();
			}, 500);
		});
	}
};

let btnFilter = new Button({
	type: 'icon-text',
	text: "Filteren",
	icon: "filter",
	backgroundColor: "yellow",
	onclick: "openFilter"
});

let btnClaim = new Button({
	type: 'icon-text',
	text: "Claim",
	icon: "hand-grab",
	backgroundColor: "green",
	onclick: "claim"
});

let btnShow = new Button({
	type: "icon-text",
	text: "Toon details",
	icon: "eye",
	backgroundColor: "yellow",
	onclick: "showDetails"
});

Helpers.addButtonToPageTitle(btnFilter);
Helpers.addButtonToPageTitle(btnClaim);
Helpers.addButtonToPageTitle(btnShow);