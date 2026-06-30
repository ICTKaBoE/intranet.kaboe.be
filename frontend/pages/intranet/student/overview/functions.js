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

let btnChangePassword = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "key",
  title: "Wachtwoord wijzigen",
  bgColor: "red",
  modal: "changePassword",
  onclick: () => {
    Form.GetInstance(`${pageId}ChangePassword`).setLastLoadedId(
      Table.GetInstance(pageId)
        .getSelectedRowData()
        .map((r) => r.guid || r.id)
        .join("_"),
    );
  },
});

Component.addActionButton(btnFilter, btnChangePassword);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnChangePassword, ">0");
});
