import Button from "../../../../shared/default/js/object/Button.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

let btnSetPresence = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "pencil",
  title: "Aanwezigheden registreren",
  bgColor: "green",
  onclick: "edit",
});

Component.addActionButton(btnFilter, btnSetPresence);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnSetPresence, "==1");
});
