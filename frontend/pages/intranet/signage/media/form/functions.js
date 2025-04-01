import Select from "../../../../../shared/default/js/object/Select.js";

window.typeView = (info) => {
	let type = Select.GetInstance("type").getValue();

	if (type === "I")
		document.getElementById("type-I").classList.remove("d-none");
	else document.getElementById("type-I").classList.add("d-none");

	if (type === "V")
		document.getElementById("type-V").classList.remove("d-none");
	else document.getElementById("type-V").classList.add("d-none");

	if (type === "L")
		document.getElementById("type-L").classList.remove("d-none");
	else document.getElementById("type-L").classList.add("d-none");
};
