import Calendar from "../../../../shared/default/js/object/Calendar.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";

window.setRide = (info) => {
	let data = new FormData();
	data.append("date", info.dateStr);

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
};
