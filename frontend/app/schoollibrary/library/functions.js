import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Form from "../../../shared/default/js/object/Form.js";
import Select from "../../../shared/default/js/object/Select.js";

window.loadTable = (value) => {
    Table.INSTANCES[`tbl${pageId}`].addExtraData("schoolId", value);
    Table.INSTANCES[`tbl${pageId}`].reload();
};

window.add = () => {
    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("create");
    Helpers.toggleModal("library");
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
    Helpers.toggleModal("library");
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
    Helpers.toggleModal("library");
};

window.showHistory = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 lijn te selecteren!");
        return;
    }

    fetch(
        document.getElementById("modal-library-history").dataset.source +
        `/actions/${values}`
    )
        .then((res) => res.json())
        .then((json) => {
            document.getElementById("libraryActionContainer").innerHTML =
                json.html;
        });

    Helpers.toggleModal("library-history");
};

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

let btnHistory = new Button(null, {
    type: "icon",
    title: "Geschiedenis",
    icon: "history",
    bgColor: "blue",
    onclick: "showHistory",
});

Helpers.addFloatingButton(btnAdd, btnEdit, btnHistory, btnDelete);

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnEdit, "==1");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnDelete, ">0");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnHistory, "==1");
});
