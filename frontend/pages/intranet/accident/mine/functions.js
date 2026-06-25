import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Table from "../../../../shared/default/js/object/Table.js";

let btnAdd = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "plus",
  title: "Toevoegen",
  bgColor: "green",
  onclick: () => {
    Helpers.redirect("/add");
  },
});

let btnFast = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "ambulance",
  title: "Fast Track",
  bgColor: "red",
  modal: "fast",
  onclick: () => {
    Form.GetInstance(`${pageId}Fast`).reset();
  },
});

let btnEdit = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "pencil",
  title: "Bewerken",
  bgColor: "orange",
  onclick: "edit",
});

Component.addActionButton(btnFast, btnAdd, btnEdit);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnEdit, "==1");
});
