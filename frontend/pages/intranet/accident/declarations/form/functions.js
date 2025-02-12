import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.renderOptgroupItem = (data, escape) => {
	return (
		"<div>" +
		(data.optgroupName ? `${data.optgroupName} - ` : "") +
		`${data.name}</div>`
	);
};

window.locationView = (info) => {
	let location = Select.GetInstance("location").getValue();
	location = location.split("-")[0];

	if (location === "O")
		document.getElementById("location-O").classList.remove("d-none");
	else document.getElementById("location-O").classList.add("d-none");
};

window.partyView = (info) => {
	let party = Select.GetInstance("party").getValue();

	if (party === "E") {
		document.getElementById("party-E").classList.remove("d-none");
		document.getElementById("party-O").classList.add("d-none");
		document.getElementById("party-I").classList.add("d-none");
	} else if (party === "O") {
		document.getElementById("party-E").classList.add("d-none");
		document.getElementById("party-O").classList.remove("d-none");
		document.getElementById("party-I").classList.add("d-none");
	} else if (party === "I") {
		document.getElementById("party-E").classList.add("d-none");
		document.getElementById("party-O").classList.add("d-none");
		document.getElementById("party-I").classList.remove("d-none");
	}
};

window.policeView = () => {
	let police = Checkbox.GetInstance("chbPolice").getValue();

	if (police) document.getElementById("police-Y").classList.remove("d-none");
	else document.getElementById("police-Y").classList.add("d-none");
};

window.supervisionView = () => {
	let supervision = Checkbox.GetInstance("chbSupervision").getValue();

	if (supervision)
		document.getElementById("supervision-Y").classList.remove("d-none");
	else document.getElementById("supervision-Y").classList.add("d-none");
};

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

Component.addActionButton(btnCancel, btnSave);

Helpers.CheckAllLoaded(() => {
	setTimeout(() => {
		window.locationView();
		window.partyView();
		window.policeView();
		window.supervisionView();
	}, 250);

	setTimeout(() => {
		Select.GetInstance("informatStudentRelationId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentRelationId").reload();

		Select.GetInstance("informatStudentEmailId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentEmailId").reload();

		Select.GetInstance("informatStudentNumberId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentNumberId").reload();

		Select.GetInstance("informatStudentBankId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentBankId").reload();

		Select.GetInstance("informatStudentAddressId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentAddressId").reload();
	}, 500);
});
