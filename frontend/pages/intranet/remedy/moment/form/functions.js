import Select from "../../../../../shared/default/js/object/Select.js";

window.departmentView = (info) => {
	let department = Select.GetInstance("departmentId").getItemDetails();
	document.getElementById("schoolId").value = department.schoolId;

	Select.GetInstance("buildingId").setExtraLoadParam(
		"schoolId",
		department.schoolId
	);
	Select.GetInstance("buildingId").reload();

	Select.GetInstance("hourId").setExtraLoadParam(
		"schoolId",
		department.schoolId
	);
	Select.GetInstance("hourId").reload();
};

window.typeView = (info) => {
	let details = Select.GetInstance("typeId").getItemDetails();

	if (details.courseDependsOnSkore) {
		Select.GetInstance("courseId").hide();
		Select.GetInstance("informatEmployeeId").hide();

		if (add) {
			Select.GetInstance("repeat").enable();
			document.getElementById("startDate").removeAttribute("disabled");
		} else {
			document
				.getElementById("date")
				.parentElement.classList.remove("d-none");
		}
	} else {
		Select.GetInstance("courseId").show();
		Select.GetInstance("informatEmployeeId").show();

		if (add) {
			Select.GetInstance("repeat").disable();
			document.getElementById("startDate").setAttribute("disabled", null);
		} else {
			document
				.getElementById("date")
				.parentElement.classList.add("d-none");
		}
	}
};

window.repeatView = (info) => {
	let val = Select.GetInstance("repeat").getValue();

	if (val === "N")
		document.getElementById("endDate").setAttribute("disabled", null);
	else document.getElementById("endDate").removeAttribute("disabled");
};
