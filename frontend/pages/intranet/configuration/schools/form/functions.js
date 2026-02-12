import Select from "../../../../../shared/default/js/object/Select.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.virtualView = (info) => {
	let val = Checkbox.GetInstance("chbVirtual").getValue();

	if (val) {
		Select.GetInstance("parentSchoolId").disable();
		document.getElementById("parentSchool-warning").classList.add("d-none");
	} else {
		Select.GetInstance("parentSchoolId").enable();
		document
			.getElementById("parentSchool-warning")
			.classList.remove("d-none");
	}
};

window.syncView = (info) => {
	let val = Checkbox.GetInstance("chbSync").getValue();

	if (val) document.getElementById("sync-Y").classList.remove("d-none");
	else document.getElementById("sync-Y").classList.add("d-none");
};

$(document).ready(() => {
	window.virtualView();
	window.syncView();
});
