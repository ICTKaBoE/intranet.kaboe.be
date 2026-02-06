import Select from "../../../../../shared/default/js/object/Select.js";

window.substituteByView = (info) => {
	let location = Select.GetInstance("substituteBy").getValue();

	if (location == window.SELECT_OTHER_ID)
		document.getElementById("substituteBy-O").classList.remove("d-none");
	else document.getElementById("substituteBy-O").classList.add("d-none");
};

window.paymentOfSubstituteView = (info) => {
	let location = Select.GetInstance("paymentOfSubstitute").getValue();

	if (location == window.SELECT_OTHER_ID)
		document
			.getElementById("paymentOfSubstitute-O")
			.classList.remove("d-none");
	else
		document
			.getElementById("paymentOfSubstitute-O")
			.classList.add("d-none");
};
