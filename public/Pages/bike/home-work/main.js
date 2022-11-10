import Calendar from '../../../JS/objects/Calendar.js';

window.setRide = (info) => {
	$.post(Calendar.INSTANCES[calendarId].action, {
		date: info.dateStr,
	}).done((data) => {
		data = JSON.parse(data);

		if (data.reload) Calendar.INSTANCES[calendarId].reload();
	}).fail();
};