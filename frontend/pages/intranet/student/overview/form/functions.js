import Button from "../../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";

let btnPrint = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "printer",
  title: "Printen",
  bgColor: "blue",
  modal: "printSelect",
  onclick: "",
});

Component.addActionButton(btnPrint);
