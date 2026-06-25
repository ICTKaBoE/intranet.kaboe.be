import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Form from "../../../../shared/default/js/object/Form.js";

let btnView = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "eye",
  title: "Bekijken",
  bgColor: "green",
  onclick: "edit",
});

let btnCancel = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "x",
  title: "Annuleren",
  bgColor: "red",
  modal: "cancel",
  onclick: () => {
    Form.GetInstance(`${pageId}Cancel`).setLastLoadedId(
      Table.GetInstance(pageId)
        .getSelectedRowData()
        .map((r) => r.guid || r.id)
        .join("_"),
    );
  },
});

Component.addActionButton(btnView, btnCancel);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnView, "==1");
  Table.GetInstance(pageId).attachButton(btnCancel, ">0");
});
