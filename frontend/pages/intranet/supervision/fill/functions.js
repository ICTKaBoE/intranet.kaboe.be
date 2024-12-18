import Form from "../../../../shared/default/js/object/Form.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";

window.setTime = (info) => {
	let data = info?.event ?? info;

	Form.GetInstance(`${pageId}Set`).prefillForm(data.id);

	setTimeout(() => {
		Form.GetInstance(`${pageId}Set`).setField(
			"date",
			data.startStr.split("T")[0]
		);
		Form.GetInstance(`${pageId}Set`).setField(
			"start",
			data.startStr.split("T")[1].split("+")[0]
		);
		Form.GetInstance(`${pageId}Set`).setField(
			"end",
			data.endStr.split("T")[1].split("+")[0]
		);
	}, 100);

	Helpers.toggleModal("set");
};

window.removeTime = (info) => {
	let data = info?.event ?? info;

	Form.GetInstance(`${pageId}Delete`).setLastLoadedId(data.id);
	Helpers.toggleModal("delete");
};
