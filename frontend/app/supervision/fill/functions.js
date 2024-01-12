import Form from "../../../shared/default/js/object/Form.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";

window.setTime = (info) => {
	let data = info?.event ?? info;

	Form.GetInstance(`frm${pageId}`).prefillForm(data.id);

	setTimeout(() => {
		Form.GetInstance(`frm${pageId}`).setField("date", data.startStr.split("T")[0]);
		Form.GetInstance(`frm${pageId}`).setField("start", data.startStr.split("T")[1].split("+")[0]);
		Form.GetInstance(`frm${pageId}`).setField("end", data.endStr.split("T")[1].split("+")[0]);
		Form.GetInstance(`frm${pageId}`).setActiveType(data.id ? "update" : "create");
	}, 100);

	Helpers.toggleModal("fill");
};

window.removeTime = (info) => {
	let data = info?.event ?? info;

	Form.GetInstance(`frm${pageId}`).reset();
	Form.GetInstance(`frm${pageId}`).setActiveType("delete");
	Form.GetInstance(`frm${pageId}`).setField("ids", data.id);
	Helpers.toggleModal("fill");
};