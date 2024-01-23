import Form from "../../../shared/default/js/object/Form.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";

window.filter = () => {
    Helpers.toggleModal("helpdesk-ticket-filter");
};

window.applyFilter = () => {
    let school = Select.INSTANCES["filterSchool"].getValue();
    let status = Select.INSTANCES["filterStatus"].getValue();
    let priority = Select.INSTANCES["filterPriority"].getValue();

    Table.GetInstance(pageId).addExtraData("school", school);
    Table.GetInstance(pageId).addExtraData("status", status);
    Table.GetInstance(pageId).addExtraData("priority", priority);
    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("helpdesk-ticket-filter");
};

window.emptyFilter = () => {
    Select.INSTANCES["filterSchool"].reload();
    Select.INSTANCES["filterStatus"].reload();
    Select.INSTANCES["filterPriority"].reload();

    Table.GetInstance(pageId).clearExtraData();
    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("helpdesk-ticket-filter");
};

window.view = () => {
    let values = Table.GetInstance(pageId).getSelectedValue();

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 ticket te selecteren!");
        return;
    }

    Form.GetInstance(`${pageId}Thread`).reset();
    Form.GetInstance(`${pageId}Details`).reset();
    Form.GetInstance(`${pageId}Details`).prefillForm(values);
    fetch(
        document.getElementById("modal-helpdesk-ticket-view").dataset.source +
        `/thread/${values}`
    )
        .then((res) => res.json())
        .then((json) => {
            document.getElementById("helpdeskThreadContainer").innerHTML =
                json.html;
        });

    fetch(
        document.getElementById("modal-helpdesk-ticket-view").dataset.source +
        `/action/${values}`
    )
        .then((res) => res.json())
        .then((json) => {
            document.getElementById("helpdeskActionContainer").innerHTML =
                json.html;
        });

    Helpers.toggleModal("helpdesk-ticket-view");
};

let btnFilter = new Button(null, {
    type: "icon",
    title: "Filteren",
    icon: "filter",
    bgColor: "primary",
    onclick: "filter",
});

let btnView = new Button(null, {
    type: "icon",
    title: "Bekijken",
    icon: "eye",
    bgColor: "warning",
    onclick: "view",
});

Helpers.addFloatingButton(btnFilter, btnView);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnView, "==1");
});
