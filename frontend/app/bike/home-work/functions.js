import Calendar from "../../../shared/ui/js/custom/objects/Calendar.js";

window.setRide = (info) => {
	$.post(Calendar.INSTANCES[calendarId].action, {
		date: info.dateStr,
	}).done((data) => {
	}).fail()
		.always((returnData) => {
			let data = JSON.parse(returnData.responseText || JSON.stringify(returnData));

			if (data.reload) Calendar.INSTANCES[calendarId].reload();
		});
};