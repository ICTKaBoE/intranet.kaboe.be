import Select from "../../../../../shared/default/js/object/Select.js";

window.descriptionView = (info) => {
	let val = Select.GetInstance("description").getValue();

	if (val === "O")
		document.getElementById("description-O").classList.remove("d-none");
	else document.getElementById("description-O").classList.add("d-none");
};

window.firstHelpView = (info) => {
	let val = Select.GetInstance("firstHelp").getValue();

	if (val === "O")
		document.getElementById("firstHelp-O").classList.remove("d-none");
	else document.getElementById("firstHelp-O").classList.add("d-none");
};

window.setVictim = () => {
	let selected = Select.GetInstance("victimType").getValue();
	Select.GetInstance("victimId").setDetails(selected);
};
