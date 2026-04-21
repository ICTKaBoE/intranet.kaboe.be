import Checkbox from "../../../../shared/default/js/object/Checkbox.js";
import Select from "../../../../shared/default/js/object/Select.js";
import DatePicker from "../../../../shared/default/js/object/DatePicker.js";

window.useTemplateView = () => {
	if (Checkbox.GetInstance("useTemplate").getValue()) {
		Select.GetInstance("template").show();
		document.getElementById("content").setAttribute("disabled", null);
	} else {
		Select.GetInstance("template").hide();
		document.getElementById("content").removeAttribute("disabled");
	}
};

window.sendNowView = () => {
	if (Checkbox.GetInstance("sendNow").getValue())
		document.getElementById("datetime_view").classList.add("d-none");
	else document.getElementById("datetime_view").classList.remove("d-none");
};

window.templateChange = () => {
	document.getElementById("content").value =
		Select.GetInstance("template").getItemDetails().content;
};

$(document).ready(() => {
	let contentLength = document.getElementById("content_length");
	let content = document.getElementById("content");

	contentLength.innerHTML = `0/${content.maxLength}`;

	content.onkeyup = (v) => {
		contentLength.innerHTML = `${content.value.length}/${content.maxLength}`;
	};

	setTimeout(() => {
		window.useTemplateView();
		window.sendNowView();
	}, 250);
});
