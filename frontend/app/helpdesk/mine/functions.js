import Form from "../../../shared/default/js/object/Form.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";

window.add = () => {
    Form.GetInstance(`frm${pageId}New`).reset();
    Helpers.toggleModal("helpdesk-ticket-new");
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

window.check = () => {
    let new_deviceLocation = document.getElementById("new_deviceLocation");
    let new_deviceBrand = document.getElementById("new_deviceBrand");
    let new_deviceType = document.getElementById("new_deviceType");

    let school = Select.GetInstance("new_schoolId").getValue();
    let type = Select.GetInstance("new_type").getValue();

    if (type === "L" || type === "D") {
        Select.GetInstance("new_subtype").enable();
        Select.GetInstance("new_deviceName").enable();
        Select.GetInstance("new_deviceName").setDetails("computer");
        Select.GetInstance("new_deviceName").setExtraLoadParam(
            "schoolId",
            school
        );
        Select.GetInstance("new_deviceName").setExtraLoadParam("type", type);

        new_deviceLocation.value = "";
        new_deviceBrand.value = "";
        new_deviceType.value = "";
    } else if (type === "P") {
        Select.GetInstance("new_subtype").disable();
        Select.GetInstance("new_deviceName").enable();
        Select.GetInstance("new_deviceName").setDetails("printer");
        Select.GetInstance("new_deviceName").setExtraLoadParam(
            "schoolId",
            school
        );

        new_deviceLocation.value = "";
        new_deviceBrand.value = "";
        new_deviceType.value = "";
    } else if (type === "B") {
        Select.GetInstance("new_subtype").disable();
        Select.GetInstance("new_deviceName").enable();
        Select.GetInstance("new_deviceName").setDetails("beamer");
        Select.GetInstance("new_deviceName").setExtraLoadParam(
            "schoolId",
            school
        );

        new_deviceLocation.value = "";
        new_deviceBrand.value = "";
        new_deviceType.value = "";
    } else {
        Select.GetInstance("new_subtype").disable();
        Select.GetInstance("new_deviceName").disable();

        new_deviceLocation.value = "";
        new_deviceBrand.value = "";
        new_deviceType.value = "";
    }
};

window.fillRequiredFields = () => {
    let details = Select.GetInstance("new_deviceName").getItemDetails();

    if (details?.brand)
        document.getElementById("new_deviceBrand").value = details.brand;
    if (details?.type)
        document.getElementById("new_deviceType").value = details.type;
};

let btnAdd = new Button(null, {
    type: "icon",
    title: "Toevoegen",
    icon: "plus",
    bgColor: "primary",
    onclick: "add",
});

let btnView = new Button(null, {
    type: "icon",
    title: "Bekijken",
    icon: "eye",
    bgColor: "warning",
    onclick: "view",
});

Helpers.addFloatingButton(btnAdd, btnView);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnView, "==1");
});
