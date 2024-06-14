import Form from "../../../shared/default/js/object/Form.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";

window.view = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 bestelling te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("update");
    Form.GetInstance(`frm${pageId}`).prefillForm(values);

    Table.INSTANCES[`tbl${pageId}Line`].addExtraData("orderId", values);
    Table.INSTANCES[`tbl${pageId}Line`].reload();

    Helpers.toggleModal("view");
};

window.accept = () => {
    Select.GetInstance("status").setValue("A");
    setTimeout(() => {
        Form.GetInstance(`frm${pageId}`).submit();
    }, 150);
};

window.deny = () => {
    Select.GetInstance("status").setValue("D");
    setTimeout(() => {
        Form.GetInstance(`frm${pageId}`).submit();
    }, 150);
};

let btnView = new Button(null, {
    type: "icon",
    title: "Bekijken",
    icon: "eye",
    bgColor: "yellow",
    onclick: "view",
});

Helpers.addFloatingButton(btnView);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnView, "==1");

    Button.GetInstance("Accept").setOnClick("accept");
    Button.GetInstance("Deny").setOnClick("deny");
});
