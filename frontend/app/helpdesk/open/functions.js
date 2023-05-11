import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import Table from "../../../shared/ui/js/custom/objects/Table.js";

window.showDetails = () => {
	let id = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
	Helpers.redirect(`/../details?id=${id}`);
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

let btnClaim = new Button({
	type: 'icon-text',
	text: "Claim",
	icon: "hand-grab",
	backgroundColor: "green",
	onclick: "claim"
});

Helpers.addButtonToPageTitle(btnClaim);

setInterval(() => {
	Table.INSTANCES[`tbl${pageId}`].reload();
}, 10000);