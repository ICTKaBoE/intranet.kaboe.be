import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.checkedView = (info) => {
	let obs = Boolean(Checkbox.GetInstance("OwnedBySchool").getValue());
	let sto = Boolean(Checkbox.GetInstance("SchoolTakesOwnership").getValue());

	if (obs || sto) {
		document.getElementById("manual").removeAttribute("disabled");
		document.getElementById("ce").removeAttribute("disabled");
	} else {
		document.getElementById("manual").setAttribute("disabled", null);
		document.getElementById("ce").setAttribute("disabled", null);
	}

	if (obs) Checkbox.GetInstance("SchoolTakesOwnership").disable();
	else Checkbox.GetInstance("SchoolTakesOwnership").enable();

	if (!sto && !obs)
		document.getElementById("remove-danger").classList.remove("d-none");
	else document.getElementById("remove-danger").classList.add("d-none");
};

$(document).ready(() => {
	window.checkedView();
});
