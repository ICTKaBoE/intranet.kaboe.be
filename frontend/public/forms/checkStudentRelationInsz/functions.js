import Select from './../../../shared/default/js/object/Select.js';

window.loadStudents = () => {
	let schoolId = Select.GetInstance('schoolId').getValue();
	let classId = Select.GetInstance('classId').getValue();
	let studentId = Select.GetInstance('studentId');

	studentId.setExtraLoadParam('school', schoolId);
	studentId.setExtraLoadParam('class', classId);
	studentId.reload();
};