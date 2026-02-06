import Helpers from "./object/Helpers.js";
import Table from "./object/Table.js";
import Select from "./object/Select.js";

window.edit = () => {
	let selected = Table.GetInstance(pageId).getSelectedRowData();
	Helpers.redirect(`/${selected[0].guid || selected[0].id}`);
};

window.view = () => edit();

window.filter = () => {
	let modal = document.getElementById("modal-filter");
	let inputs = $(modal).find(":input,[role]");

	let data = {};
	inputs.each((id, el) => {
		if (null === el) return;
		if (el.type === "checkbox" || el.type === "radio") return;

		let name = el.name;
		let value = el.value;

		if (el.role === "select") {
			let v = Select.GetInstance(el.id).getValue();
			data[name] = typeof v == "string" ? v : v.join(";");
		} else data[name] = value;

		if (!name) delete data[name];
	});

	$.each(data, (key, val) => {
		Table.GetInstance(pageId).addExtraData(key, val);
	});

	Helpers.closeAllModals();
	Table.GetInstance(pageId).reload();
};

window.emptyFilter = () => {
	let modal = document.getElementById("modal-filter");
	let inputs = $(modal).find(":input,[role]");

	inputs.each((id, el) => {
		if (null === el) return;
		if (el.type === "checkbox" || el.type === "radio") return;

		let name = el.name;
		let value = el.value;

		if (el.role === "select") Select.GetInstance(el.id).clear();
		else el.value = "";
	});

	filter();
};
