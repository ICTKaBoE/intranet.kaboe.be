import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.ownedBySchoolView = (info) => {
	if (Boolean(Checkbox.GetInstance("OwnedBySchool").getValue())) {
		Checkbox.GetInstance("SchoolTakesOwnership").disable();
		document.getElementById("manual").removeAttribute("disabled");
		document.getElementById("ce").removeAttribute("disabled");
	} else {
		Checkbox.GetInstance("SchoolTakesOwnership").enable();
		document.getElementById("manual").setAttribute("disabled", null);
		document.getElementById("ce").setAttribute("disabled", null);
	}
};

window.schoolTakesOwnershipView = (info) => {
	if (Boolean(Checkbox.GetInstance("SchoolTakesOwnership").getValue())) {
		document.getElementById("manual").removeAttribute("disabled");
		document.getElementById("ce").removeAttribute("disabled");
	} else {
		document.getElementById("manual").setAttribute("disabled", null);
		document.getElementById("ce").setAttribute("disabled", null);
	}
};

$(document).ready(() => {
	window.ownedBySchoolView();
});
