import Form from "../../../shared/default/js/object/Form.js";
import Button from "../../../shared/default/js/object/Button.js";
import Helpers from "../../../shared/default/js/object/Helpers.js";
import Table from "../../../shared/default/js/object/Table.js";
import Select from "../../../shared/default/js/object/Select.js";

window.filter = () => {
    Helpers.toggleModal("filter");
};

window.applyFilter = () => {
    let school = Select.INSTANCES["filterSchool"].getValue();
    let status = Select.INSTANCES["filterStatus"].getValue();

    Table.GetInstance(pageId).addExtraData("school", school);
    Table.GetInstance(pageId).addExtraData("status", status);
    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("filter");
};

window.emptyFilter = () => {
    Select.INSTANCES["filterSchool"].reload();
    Select.INSTANCES["filterStatus"].reload();

    Table.GetInstance(pageId).clearExtraData();
    Table.GetInstance(pageId).reload();
    Helpers.toggleModal("filter");
};

window.add = () => {
    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("create");

    Table.INSTANCES[`tbl${pageId}Line`].clearExtraData();
    Table.INSTANCES[`tbl${pageId}Line`].reload();

    Helpers.toggleModal("order");
};

window.edit = () => {
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

    Helpers.toggleModal("order");
};

window.delete = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0) {
        alert("Gelieve 1 of meerdere bestellingen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("delete");
    Form.GetInstance(`frm${pageId}`).setField("ids", values);
    Helpers.toggleModal("order");
};

window.addLine = () => {
    Form.GetInstance(`frm${pageId}Line`).reset();
    Form.GetInstance(`frm${pageId}Line`).setField('orderId', Form.GetInstance(`frm${pageId}`).lastLoadedId);
    Form.GetInstance(`frm${pageId}Line`).setActiveType("create");
    Helpers.toggleModal("order-line");
};

window.editLine = () => {
    let values = Table.INSTANCES[`tbl${pageId}Line`].getSelectedValue();

    if (values.length === 0 || values.includes("-")) {
        alert("Gelieve 1 bestellijn te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}Line`).reset();
    Form.GetInstance(`frm${pageId}Line`).setActiveType("update");
    Form.GetInstance(`frm${pageId}Line`).prefillForm(values);

    Helpers.toggleModal("order-line");
};

window.deleteLine = () => {
    let values = Table.INSTANCES[`tbl${pageId}Line`].getSelectedValue();

    if (values.length === 0) {
        alert("Gelieve 1 of meerdere bestellijnen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}Line`).reset();
    Form.GetInstance(`frm${pageId}Line`).setActiveType("delete");
    Form.GetInstance(`frm${pageId}Line`).setField("lids", values);
    Helpers.toggleModal("order-line");
};

window.requestQuote = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0) {
        alert("Gelieve 1 of meerdere bestellingen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}RequestQuote`).reset();
    Form.GetInstance(`frm${pageId}RequestQuote`).setField("rqids", values);
    Helpers.toggleModal("order-request-quote");
};

window.requestAccept = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0) {
        alert("Gelieve 1 of meerdere bestellingen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}RequestAccept`).reset();
    Form.GetInstance(`frm${pageId}RequestAccept`).setField("raids", values);
    Helpers.toggleModal("order-request-accept");
};

window.postOrder = () => {
    let values = Table.INSTANCES[`tbl${pageId}`].getSelectedValue();

    if (values.length === 0) {
        alert("Gelieve 1 of meerdere bestellingen te selecteren!");
        return;
    }

    Form.GetInstance(`frm${pageId}PostOrder`).reset();
    Form.GetInstance(`frm${pageId}PostOrder`).setField("poids", values);
    Helpers.toggleModal("order-post");
};

window.check = () => {
    let _for = Select.GetInstance("for").getValue();
    let _schoolId = Select.GetInstance("schoolId").getValue();
    let _assetId = Select.GetInstance("assetId");

    if (_for == "O") {
        _assetId.disable();
    } else if (_for == "L" || _for == "D") {
        _assetId.enable();
        _assetId.setDetails("computer");
        _assetId.setExtraLoadParam("schoolId", _schoolId);
        _assetId.setExtraLoadParam("type", _for);
    } else if (_for == "B") {
        _assetId.enable();
        _assetId.setDetails("beamer");
        _assetId.setExtraLoadParam("schoolId", _schoolId);
    } else if (_for == "P") {
        _assetId.enable();
        _assetId.setDetails("printer");
        _assetId.setExtraLoadParam("schoolId", _schoolId);
    }
};

let btnFilter = new Button(null, {
    type: "icon",
    title: "Filteren",
    icon: "filter",
    bgColor: "primary",
    onclick: "filter",
});

let btnAdd = new Button(null, {
    type: "icon",
    title: "Toevoegen",
    icon: "plus",
    bgColor: "primary",
    onclick: "green",
});

let btnEdit = new Button(null, {
    type: "icon",
    title: "Bewerken",
    icon: "pencil",
    bgColor: "warning",
    onclick: "edit",
});

let btnRequestQuote = new Button(null, {
    type: "icon",
    title: "Offerte aanvragen",
    icon: "receipt",
    bgColor: "yellow",
    onclick: "requestQuote"
});

let btnRequestAccept = new Button(null, {
    type: "icon",
    title: "Goedkeuring aanvragen",
    icon: "file-check",
    bgColor: "green",
    onclick: "requestAccept"
});

let btnPostOrder = new Button(null, {
    type: "icon",
    title: "Bestelling plaatsen",
    icon: "receipt-euro",
    bgColor: "lime",
    onclick: "postOrder"
});

let btnDelete = new Button(null, {
    type: "icon",
    title: "Verwijderen",
    icon: "trash",
    bgColor: "danger",
    onclick: "delete",
});

let btnAddLine = new Button(null, {
    type: "icon",
    title: "Toevoegen",
    icon: "plus",
    bgColor: "green",
    onclick: "addLine",
});

let btnEditLine = new Button(null, {
    type: "icon",
    title: "Bewerken",
    icon: "pencil",
    bgColor: "warning",
    onclick: "editLine",
});

let btnDeleteLine = new Button(null, {
    type: "icon",
    title: "Verwijderen",
    icon: "trash",
    bgColor: "danger",
    onclick: "deleteLine",
});

Helpers.addFloatingButton(btnFilter, btnAdd, btnEdit, btnRequestQuote, btnRequestAccept, btnPostOrder, btnDelete);

document
    .getElementById(`tbl${pageId}LineButtons`)
    .appendChild(btnAddLine.write());
document
    .getElementById(`tbl${pageId}LineButtons`)
    .appendChild(btnEditLine.write());
document
    .getElementById(`tbl${pageId}LineButtons`)
    .appendChild(btnDeleteLine.write());

$(document).ready(() => {
    Table.GetInstance(`tbl${pageId}`).attachButton(btnEdit, "==1");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnRequestQuote, ">0");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnRequestAccept, ">0");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnPostOrder, ">0");
    Table.GetInstance(`tbl${pageId}`).attachButton(btnDelete, ">0");

    Table.GetInstance(`tbl${pageId}Line`).attachButton(btnEditLine, "==1");
    Table.GetInstance(`tbl${pageId}Line`).attachButton(btnDeleteLine, ">0");
});
