import Button from "../../../../shared/default/js/object/Button.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnChangePassword = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "password",
  title: "Wijzig Wachtwoord",
  bgColor: "danger",
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

Component.addActionButton(btnChangePassword);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnChangePassword, ">0");
});
