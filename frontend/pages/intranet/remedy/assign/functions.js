import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Calendar from "../../../../shared/default/js/object/Calendar.js";

window.dropEvent = (info) => {
	info = info?.event || info;

	let data = new FormData();
	data.append("date", info?.startStr ?? info.dateStr);
	data.append("id", info?.id ?? info.draggedEl.dataset.id);

	Helpers.request({
		url: Calendar.GetInstance(pageId).action,
		method: "POST",
		data: data,
		always: (returnData) => {
			let data = JSON.parse(
				returnData.responseText || JSON.stringify(returnData)
			);

			Helpers.processRequestResponse(data);
		},
	});

	Calendar.GetInstance(calendarId).revert();
};

window.eventDataFormat = (eventEl) => {
	return {
		id: eventEl.dataset.id,
		title: eventEl.dataset.title,
		color: eventEl.dataset.color,
	};
};

window.removeEvent = (info) => {
	let x = info.jsEvent.clientX;
	let y = info.jsEvent.clientY;
	let el = document.elementFromPoint(x, y);

	if ($(el).closest(".removeEventContainer").length) {
		let data = new FormData();
		data.append("id", info.event.id);
		data.append("remove", true);

		Helpers.request({
			url: Calendar.GetInstance(pageId).action,
			method: "POST",
			data: data,
			always: (returnData) => {
				let data = JSON.parse(
					returnData.responseText || JSON.stringify(returnData)
				);

				Helpers.processRequestResponse(data);
			},
		});
	}
};
