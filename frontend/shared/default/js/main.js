import Select from "./object/Select.js";
import Form from "./object/Form.js";
import Toast from "./object/Toast.js";
import Table from "./object/Table.js";
import Calendar from "./object/Calendar.js";
import DatePicker from "./object/DatePicker.js";
import TinyMCE from "./object/TinyMCE.js";
import Chart from "./object/Chart.js";
import ColorInput from "./object/ColorInput.js";
import SearchField from "./object/SearchField.js";
import List from "./object/List.js";
import Checkbox from "./object/Checkbox.js";
import Signage from "./object/Signage.js";
import Rating from "./object/Rating.js";
import Helpers from "./object/Helpers.js";

window.SELECT_OTHER_ID = -1;

$.ajaxSetup({
  xhrFields: {
    mode: "cors",
    withCredentials: true,
  },
});


window.createProgressBar = () => {
  let progress = document.createElement("div");
  progress.classList.add("progress");
  progress.classList.add("d-none");
  let progressBar = document.createElement("div");
  progressBar.classList.add("progress-bar");
  progressBar.classList.add("progress-bar-indeterminate");
  progressBar.classList.add("bg-green");
  progress.appendChild(progressBar);

  return progress;
};

const components = [
  Toast,
  List,
  Select,
  Checkbox,
  TinyMCE,
  Table,
  Calendar,
  DatePicker,
  Chart,
  Rating,
  ColorInput,
  SearchField,
];

components.forEach((c) => c.ScanAndCreate());

// Uitgestelde initialisaties
setTimeout(() => {
  [Form, Signage].forEach((c) => c.ScanAndCreate());
}, 250);

$(document).ready(() => {
  Helpers.CheckAllLoaded(() => {
    setTimeout(() => {
      window.fillFilter();

      const popoverTriggerList = document.querySelectorAll(
        '[data-bs-toggle="popover"]',
      );
      const popoverList = [...popoverTriggerList].map(
        (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl),
      );
    }, 500);
  }, [Select, Table, List, Form]);
});
