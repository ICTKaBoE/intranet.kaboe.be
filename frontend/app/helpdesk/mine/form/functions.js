import Select from "../../../../shared/ui/js/custom/objects/Select.js";

window.check = (v) => {
	let deviceNameCustom = document.getElementById("deviceNameCustom");
	let deviceNameSelectWrapper = document.getElementById("deviceNameSelectWrapper");
	let deviceLocation = document.getElementById("deviceLocation");
	let deviceBrand = document.getElementById("deviceBrand");
	let deviceType = document.getElementById("deviceType");
	deviceNameCustom.value = "";

	let school = Select.INSTANCES['schoolId'].getItemDetails();
	let type = Select.INSTANCES['type'].getItemDetails();

	if (null !== type) {
		if (type.id === "O") {
			Select.INSTANCES['subtype'].disable();
			if (deviceNameCustom.classList.contains("d-none")) deviceNameCustom.classList.remove("d-none");
			if (!deviceNameSelectWrapper.classList.contains("d-none")) deviceNameSelectWrapper.classList.add("d-none");

			deviceNameCustom.setAttribute("disabled", "");
			deviceNameCustom.classList.remove("d-none");

			deviceBrand.setAttribute("disabled", "");
			deviceType.setAttribute("disabled", "");

			deviceNameCustom.value = "";
			deviceLocation.value = "";
			deviceBrand.value = "";
			deviceType.value = "";
		} else if (type.id === "B") {
			Select.INSTANCES['subtype'].disable();
			if (deviceNameCustom.classList.contains("d-none")) deviceNameCustom.classList.remove("d-none");
			if (!deviceNameSelectWrapper.classList.contains("d-none")) deviceNameSelectWrapper.classList.add("d-none");

			deviceNameCustom.setAttribute("disabled", "");
			deviceNameCustom.classList.remove("d-none");
			deviceBrand.removeAttribute("disabled");
			deviceType.removeAttribute("disabled");

			deviceNameCustom.value = "";
			deviceLocation.value = "";
			deviceBrand.value = "";
			deviceType.value = "";
		} else if (type.id === "P") {
			Select.INSTANCES['subtype'].disable();
			if (deviceNameCustom.classList.contains("d-none")) deviceNameCustom.classList.remove("d-none");
			if (!deviceNameSelectWrapper.classList.contains("d-none")) deviceNameSelectWrapper.classList.add("d-none");

			deviceNameCustom.removeAttribute("disabled");
			deviceNameCustom.classList.remove("d-none");
			deviceBrand.setAttribute("disabled", "");
			deviceType.setAttribute("disabled", "");

			deviceNameCustom.value = "RICOH-";
			deviceLocation.value = "";
			deviceBrand.value = "";
			deviceType.value = "";
		} else {
			Select.INSTANCES['subtype'].enable();
			if (!deviceNameCustom.classList.contains("d-none")) deviceNameCustom.classList.add("d-none");
			if (deviceNameSelectWrapper.classList.contains("d-none")) deviceNameSelectWrapper.classList.remove("d-none");

			deviceNameCustom.setAttribute("disabled", "");
			Select.INSTANCES["deviceNameSelect"].setExtraLoadParam("schoolId", Select.INSTANCES['schoolId'].getValue());
			Select.INSTANCES["deviceNameSelect"].setExtraLoadParam("type", Select.INSTANCES['type'].getValue());
			Select.INSTANCES["deviceNameSelect"].reload();

			deviceBrand.setAttribute("disabled", "");
			deviceType.setAttribute("disabled", "");

			deviceNameCustom.value = school.deviceNamePrefix;
			deviceLocation.value = "";
			deviceBrand.value = "";
			deviceType.value = "";
		}
	}
};