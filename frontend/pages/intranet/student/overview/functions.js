import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

let btnView = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "eye",
  title: "Bewerken",
  bgColor: "green",
  onclick: "edit",
});

let btnPrint = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "printer",
  title: "Printen",
  bgColor: "blue",
  modal: "printSelect",
  onclick: () => {
    Form.GetInstance(`${pageId}PrintSelect`).reset();
    Form.GetInstance(`${pageId}PrintSelect`).setLastLoadedId(
      Table.GetInstance(pageId)
        .getSelectedRowData()
        .map((r) => r.guid || r.id)
        .join("_"),
    );
  },
});

Component.addActionButton(btnFilter, btnView, btnPrint);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnView, "==1");
  Table.GetInstance(pageId).attachButton(btnPrint, ">0");
});
