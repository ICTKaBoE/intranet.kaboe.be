import Calendar from "../../../shared/ui/js/custom/objects/Calendar.js";
import Toast from "../../../shared/ui/js/custom/objects/Toast.js";

window.setTime = (info) => {
	let data = info?.event ?? info;

	$.post(Calendar.INSTANCES[calendarId].action, {
		id: data.id,
		start: data.startStr,
		end: data.endStr
	}).done((data) => {
	}).fail()
		.always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.reload) Calendar.INSTANCES[calendarId].reload();
			if (data.toast) Toast.INSTANCE.show("Middagtoezichten", data.toast.message, data.toast.type);
			if (data.action && data.action == "revert") info.revert();
		});
};

window.removeTime = (info) => {
	let data = info?.event ?? info;

	$.post(Calendar.INSTANCES[calendarId].action, {
		id: data.id,
		delete: true
	}).done((data) => {
	}).fail()
		.always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.reload) Calendar.INSTANCES[calendarId].reload();
			if (data.toast) Toast.INSTANCE.show("Middagtoezichten", data.toast.message, data.toast.type);
			if (data.action && data.action == "revert") info.revert();
		});
};