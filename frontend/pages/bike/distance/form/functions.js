import Select from "../../../../shared/default/js/object/Select.js";

window.changeLocationType = () => {
	let type = Select.GetInstance("type").getValue();
	Select.GetInstance("startId").setDetails(type);
};

window.calculateDoubleDistance = () => {
	document.getElementById("distanceDouble").value =
		document.getElementById("distance").value * 2;
};

$(document).ready(() => {
	$("#distance").on("change keyup", () => {
		window.calculateDoubleDistance();
	});
});
