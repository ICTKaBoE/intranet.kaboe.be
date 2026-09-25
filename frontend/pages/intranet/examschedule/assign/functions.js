import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Select from "../../../../shared/default/js/object/Select.js";

window.assignHours = () => {
  window.assign();
  Helpers.toggleModal("assign");
};

window.assign = () => {
  Form.GetInstance(pageId).reset();

  document.getElementById("assignSchoolyearId").value =
    Select.GetInstance("schoolyearId").getValue();
  let val = Table.GetInstance(pageId).getSelectedRowData()[0];

  if (val.hours === null) {
    document.getElementById("informatEmployeeId").value = val.id;
  } else {
    Form.GetInstance(pageId).prefillForm(
      Table.GetInstance(pageId)
        .getSelectedRowData()
        .map((r) => r.hours.guid || r.hours.id)
        .join("_"),
    );
  }
};

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

let btnEdit = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "pencil",
  title: "Bewerken",
  bgColor: "orange",
  onclick: "assign",
  modal: "assign",
});

let btnDelete = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "trash",
  title: "Verwijderen",
  bgColor: "red",
  modal: "delete",
  onclick: "delete",
});

Component.addActionButton(btnFilter, btnEdit, btnDelete);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnEdit, "==1");
  Table.GetInstance(pageId).attachButton(btnDelete, ">0");
});
