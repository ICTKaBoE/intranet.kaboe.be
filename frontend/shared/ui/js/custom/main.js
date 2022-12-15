import Form from './objects/Form.js';
import Calendar from './objects/Calendar.js';
import Select from './objects/Select.js';
import Table from './objects/Table.js';
import Helpers from './objects/Helpers.js';
import DatePicker from './objects/DatePicker.js';

Helpers.toggleWait();
Select.ScanAndCreate();
Calendar.ScanAndCreate();
DatePicker.ScanAndCreate();
Table.ScanAndCreate();

setTimeout(() => {
	Form.ScanAndCreate();
	Helpers.toggleWait();
}, 1000);