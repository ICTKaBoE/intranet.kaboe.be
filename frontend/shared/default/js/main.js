import Helpers from "./object/Helpers.js";
import Select from "./object/Select.js";
import Button from "./object/Button.js";
import Form from "./object/Form.js";
import Toast from "./object/Toast.js";
import Table from "./object/Table.js";
import Calendar from "./object/Calendar.js";
import DatePicker from "./object/DatePicker.js";
import TinyMCE from "./object/TinyMCE.js";
import Chart from "./object/Chart.js";
import Clock from "./object/Clock.js";
// import NoteScreen from "./object/NoteScreen.js";
import ColorInput from "./object/ColorInput.js";
import SearchField from "./object/SearchField.js";
import List from "./object/List.js";
import Checkbox from "./object/Checkbox.js";

$.ajaxSetup({
	xhrFields: {
		mode: "cors",
		withCredentials: true,
	},
});

Toast.Create();
Button.ScanAndCreate();
SearchField.ScanAndCreate();
TinyMCE.ScanAndCreate();
Select.ScanAndCreate();
Table.ScanAndCreate();
Calendar.ScanAndCreate();
DatePicker.ScanAndCreate();
Chart.ScanAndCreate();
List.ScanAndCreate();
Checkbox.ScanAndCreate();

window.checkAllLoadedCallback = () => {
	ColorInput.ScanAndCreate();
	Form.ScanAndCreate();

	Clock.ScanAndCreate();
	// NoteScreen.ScanAndCreate();
};

Helpers.CheckAllLoaded(window.checkAllLoadedCallback);

$(document).ready(() => {
	$("*[data-bs-dismiss='modal']").on("click", (e) => {
		try {
			let parentId = $(e.target).parents().filter(".modal.show")[0].id;
			Helpers.toggleModal(parentId.replace("modal-", ""));
		} catch {}
	});
});
