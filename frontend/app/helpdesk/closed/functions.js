import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.showDetails = () => {
	let id = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	Helpers.redirect(`/../details?id=${id}`);
};

setInterval(() => {
	Table.INSTANCES[`tbl${pageId}`].reload();
}, 10000);