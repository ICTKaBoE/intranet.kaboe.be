import Form from './objects/Form.js';
import Calendar from './objects/Calendar.js';
import Select from './objects/Select.js';
import Table from './objects/Table.js';
import Chart from './objects/Chart.js';
import DatePicker from './objects/DatePicker.js';
import Clock from './objects/Clock.js';
import NoteScreen from './objects/NoteScreen.js';
import TinyMCE from './objects/TinyMCE.js';
Select.ScanAndCreate();
Calendar.ScanAndCreate();
DatePicker.ScanAndCreate();
Table.ScanAndCreate();
Chart.ScanAndCreate();
TinyMCE.ScanAndCreate();

setTimeout(() => {
	Form.ScanAndCreate();

	Clock.ScanAndCreate();
	NoteScreen.ScanAndCreate();
}, 1000);