import Calendar from "../../../shared/default/js/object/Calendar.js";
import Toast from "../../../shared/default/js/object/Toast.js";

window.setRide = (info) => {
	$.post(Calendar.INSTANCES[calendarId].action, {
		date: info.dateStr,
	})
		.done(data => { })
		.fail(data => { })
		.always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.toast) Toast.INSTANCE.show(data.toast.title, data.toast.message, data.toast.type);
			if (data.reload) Calendar.INSTANCES[calendarId].reload();
		});
};