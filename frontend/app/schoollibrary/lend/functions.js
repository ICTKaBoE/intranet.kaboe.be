import Select from "../../../shared/default/js/object/Select.js";

window.check = () => {
	let studentOrStaff = Select.GetInstance('studentOrStaff').getValue();

	Select.GetInstance('personId').setDetails(studentOrStaff);

};
