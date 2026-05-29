import Helpers from "./object/Helpers.js";
import Table from "./object/Table.js";
import Select from "./object/Select.js";
import List from "./object/List.js";
import Form from "./object/Form.js";

window.edit = () => {
	let selected = Table.GetInstance(pageId).getSelectedRowData();
	Helpers.redirect(`/${selected[0].guid || selected[0].id}`);
};

window.delete = () => {
	Form.GetInstance(`${pageId}Delete`).setLastLoadedId(
		Table.GetInstance(pageId)
			.getSelectedRowData()
			.map((r) => r.guid || r.id)
			.join("_")
	);
};

window.view = () => edit();

window.filter = () => {
	let modal = document.getElementById("modal-filter");
	let inputs = $(modal).find(":input,[role]");

	let data = {};
	inputs.each((id, el) => {
		if (null === el) return;
		if (
			el.type === "checkbox" ||
			el.type === "radio" ||
			el.type === "button" ||
			el.type === "submit"
		)
			return;

		if (el.name) {
			if (el.role === "select") {
				let v = Select.GetInstance(el.id)?.getValue();
				data[el.name] = typeof v == "string" ? v : v.join(";");
			} else data[el.name] = el.value;
		}
	});

	$.each(data, (key, val) => {
		if (val)
			window.localStorage.setItem(
				`filter_${modal.dataset.module}_${modal.dataset.page}_${key}`,
				val
			);

		Table.GetInstance(pageId)?.addExtraData(key, val);
		List.GetInstance(pageId)?.setExtraLoadParam(key, val);
	});

	if (Object.keys(data).length) {
		if (modal.classList.contains("show")) Helpers.toggleModal("filter");
		Table.GetInstance(pageId)?.reload();
		List.GetInstance(pageId)?.reload();
	}
};

window.emptyFilter = () => {
	let modal = document.getElementById("modal-filter");
	let inputs = $(modal).find(":input,[role]");

	inputs.each((id, el) => {
		if (null === el) return;
		if (
			el.type === "checkbox" ||
			el.type === "radio" ||
			el.type === "button" ||
			el.type === "submit"
		)
			return;

		if (el.role === "select") Select.GetInstance(el.id)?.clear();
		else el.value = "";
	});

	filter();
};

window.fillFilter = () => {
	let modal = document.getElementById("modal-filter");
	let inputs = $(modal).find(":input,[role]");

	inputs.each((id, el) => {
		if (null === el) return;
		if (
			el.type === "checkbox" ||
			el.type === "radio" ||
			el.type === "button" ||
			el.type === "submit"
		)
			return;

		let v = window.localStorage.getItem(
			`filter_${modal.dataset.module}_${modal.dataset.page}_${el.name}`
		);

		if (v) {
			if (el.role === "select") Select.GetInstance(el.id)?.setValue(v);
			else el.value = v;
		}
	});

	Helpers.CheckAllLoaded(() => {
		filter();
	}, [Select, Table]);
};
