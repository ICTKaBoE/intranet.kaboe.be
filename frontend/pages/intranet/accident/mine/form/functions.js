import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Select from "../../../../../shared/default/js/object/Select.js";

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
