import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Form from "../../../shared/default/js/object/Form.js";
import Select from "../../../shared/default/js/object/Select.js";

window.add = () => {
    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("create");
    Helpers.toggleModal("management-computer");
};

window.edit = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 lijn te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("update");
    Form.GetInstance(`frm${pageId}`).prefillForm(values);
    Helpers.toggleModal("management-computer");
};

window.delete = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();
    if (values.length === 0) {
        alert("Gelieve 1 of meerdere lijnen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("delete");
    Form.GetInstance(`frm${pageId}`).setField("ids", values);
    Helpers.toggleModal("management-computer");
};

window.changeCart = () => {
    Select.GetInstance("cartId").setExtraLoadParam("type", Select.GetInstance("type").getValue());
    Select.GetInstance("cartId").reload();
}

let btnAdd = new Button(null, {
    type: "icon",
    title: "Toevoegen",
    icon: "plus",
    bgColor: "green",
    onclick: "add",
});

let btnEdit = new Button(null, {
    type: "icon",
    title: "Bewerken",
    icon: "pencil",
    bgColor: "orange",
    onclick: "edit",
});

let btnDelete = new Button(null, {
    type: "icon",
    title: "Verwijderen",
    icon: "trash",
    bgColor: "red",
    onclick: "delete",
});

Helpers.addFloatingButton(btnAdd, btnEdit, btnDelete);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnEdit, "==1");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnDelete, ">0");
});
