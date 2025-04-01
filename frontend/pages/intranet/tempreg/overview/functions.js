import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";
import DatePicker from "../../../../shared/default/js/object/DatePicker.js";

window.emptyFilter = () => {
	Select.GetInstance("schoolId").clear();
	// DatePicker.GetInstance("start").reload();
	// DatePicker.GetInstance("end").reload();

	filter();
};

window.filter = () => {
	Table.GetInstance(pageId).addExtraData(
		"schoolId",
		Select.GetInstance("schoolId").getValue()
	);
	// Table.GetInstance(pageId).addExtraData(
	// 	"start",
	// 	DatePicker.GetInstance("start").getDate()
	// );
	// Table.GetInstance(pageId).addExtraData(
	// 	"end",
	// 	DatePicker.GetInstance("end").getDate()
	// );

	Helpers.closeAllModals();
	Table.GetInstance(pageId).reload();
};

let btnFilter = new Button({
	options: {
		type: Button.TYPE_ICON,
		icon: "filter",
		title: "Filteren",
		bgColor: "blue",
		modal: "filter",
	},
});

Component.addActionButton(btnFilter);
