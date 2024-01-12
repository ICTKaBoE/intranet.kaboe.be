import Table from "../../../shared/default/js/object/Table.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Select from "../../../shared/default/js/object/Select.js";
import Button from "../../../shared/default/js/object/Button.js";
import DatePicker from "../../../shared/default/js/object/DatePicker.js";
import Clock from "../../../shared/default/js/object/Clock.js";

window.filter = () => {
    Helpers.toggleModal("meals-filter");
};

window.applyFilter = () => {
    let school = Select.INSTANCES["filterSchool"].getValue();
    let start = DatePicker.INSTANCES["filterStart"].getDate() || null;
    let end = DatePicker.INSTANCES["filterEnd"].getDate() || null;

    let timeElapsed = Date.now();
    let today = new Date(timeElapsed);

    let beginStatic = "2024-01-01T00:00:00.000Z";

    Table.GetInstance(pageId).addExtraData("schoolId", school);
    if (start != null && end != null) {
        Table.GetInstance(pageId).addExtraData("start", start.dateInstance.toISOString());
        Table.GetInstance(pageId).addExtraData("end", end.dateInstance.toISOString());
    } else if (start != null && end == null) {
        Table.GetInstance(pageId).addExtraData("start", start.dateInstance.toISOString());
        Table.GetInstance(pageId).addExtraData("end", today.toISOString());
    } else if (start == null && end != null) {
        Table.GetInstance(pageId).addExtraData("start", beginStatic);
        Table.GetInstance(pageId).addExtraData("end", end.dateInstance.toISOString());
    }

    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("meals-filter");
};

window.emptyFilter = () => {
    Select.INSTANCES["filterSchool"].reload();
    DatePicker.INSTANCES["filterStart"].reload();
    DatePicker.INSTANCES["filterEnd"].reload();

    Table.GetInstance(pageId).clearExtraData();
    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("meals-filter");
};

let btnFilter = new Button(null, {
    type: "icon",
    title: "Filteren",
    icon: "filter",
    bgColor: "primary",
    onclick: "filter",
});

Helpers.addFloatingButton(btnFilter);