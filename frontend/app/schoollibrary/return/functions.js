import Select from "../../../shared/default/js/object/Select.js";

window.check = () => {
	let studentOrStaff = Select.GetInstance('studentOrStaff').getValue();

	if (studentOrStaff == "staff") Select.GetInstance('personId').setDetails('staff');
	else if (studentOrStaff == 'student') Select.GetInstance('personId').setDetails('student');
};
