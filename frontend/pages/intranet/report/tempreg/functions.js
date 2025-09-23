import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Chart from "../../../../shared/default/js/object/Chart.js";

window.emptyFilter = () => {
	Select.GetInstance("schoolId").clear();
	document.getElementById("schoolyear").value = "";

	filter();
};

window.filter = () => {
	Chart.GetInstance(pageId).addExtraData(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
	);
	Chart.GetInstance(pageId).addExtraData(
		"schoolyear",
		document.getElementById("schoolyear").value
	);

	Helpers.closeAllModals();
	Chart.GetInstance(pageId).reload();
};

window.formatTemp = (v) => {
	return Helpers.formatValue(v, "double", { precision: 2, suffix: "°" });
};

let btnFilter = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "filter",
		title: "Filteren",
		bgColor: "blue",
		modal: "filter",
	},
});

Component.addActionButton(btnFilter);
