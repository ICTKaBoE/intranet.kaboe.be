import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";

window.changeLocationType = () => {
	let type = Select.GetInstance("type").getValue();
	Select.GetInstance("startId").setDetails(type);
};

window.calculateDoubleDistance = () => {
	document.getElementById("distanceDouble").value =
		document.getElementById("distance").value * 2;
};

Component.addActionButton(btnCancel, btnSave);

$(document).ready(() => {
	$("#distance").on("change keyup", () => {
		window.calculateDoubleDistance();
	});
});
