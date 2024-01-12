import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Form from "../../../shared/default/js/object/Form.js";
import Select from "../../../shared/default/js/object/Select.js";

window.edit = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
    let _cartId = Select.GetInstance("cartId");

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 lijn te selecteren!");
        return;
    }

    _cartId.setExtraLoadParam("type", "I");

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("update");
    Form.GetInstance(`frm${pageId}`).prefillForm(values);
    Helpers.toggleModal("management-ipad");
}

let btnEdit = new Button(null, {
    type: "icon",
    title: "Bewerken",
    icon: "pencil",
    bgColor: "orange",
    onclick: "edit",
});

Helpers.addFloatingButton(btnEdit);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnEdit, "==1");
});
