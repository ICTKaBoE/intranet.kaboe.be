import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.showDetails = () => {
	let id = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
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

Helpers.addButtonToPageTitle(btnCreate);

setInterval(() => {
	Table.INSTANCES[`tbl${pageId}`].reload();
}, 10000);