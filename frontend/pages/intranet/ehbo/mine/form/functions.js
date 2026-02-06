import Select from "../../../../../shared/default/js/object/Select.js";

window.descriptionView = (info) => {
	let val = Select.GetInstance("description").getValue();
	console.log(val);

	if (val == window.SELECT_OTHER_ID)
		document.getElementById("description-O").classList.remove("d-none");
	else document.getElementById("description-O").classList.add("d-none");
};

window.firstHelpView = (info) => {
	let val = Select.GetInstance("firstHelp").getValue();

	if (val == window.SELECT_OTHER_ID)
		document.getElementById("firstHelp-O").classList.remove("d-none");
	else document.getElementById("firstHelp-O").classList.add("d-none");
};

window.setVictim = () => {
	let selected = Select.GetInstance("victimType").getItemDetails();
	let school = Select.GetInstance("schoolId").getValue();

	if (selected.id == window.SELECT_OTHER_ID)
		Select.GetInstance("victimId").disable();
	else {
		Select.GetInstance("victimId").enable();
		Select.GetInstance("victimId").setExtraLoadParam("schoolId", school);
		Select.GetInstance("victimId").setDetails(selected.type);
	}
};
