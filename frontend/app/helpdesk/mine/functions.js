import Button from "../../../shared/ui/js/custom/objects/Button.js";
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

window.createHelpdesk = () => {
	Helpers.redirect("/new");
};

let btnCreate = new Button({
	type: "icon-text",
	text: "Aanmaken",
	icon: "plus",
	backgroundColor: "green",
	onclick: "createHelpdesk"
});

let btnShow = new Button({
	type: "icon-text",
	text: "Toon details",
	icon: "eye",
	backgroundColor: "yellow",
	onclick: "showDetails"
});

Helpers.addButtonToPageTitle(btnCreate);
Helpers.addButtonToPageTitle(btnShow);