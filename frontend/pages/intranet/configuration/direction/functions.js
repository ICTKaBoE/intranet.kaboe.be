import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnAdd = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "plus",
  title: "Toevoegen",
  bgColor: "green",
  onclick: () => {
    Helpers.redirect("/add");
  },
});

let btnEdit = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "pencil",
  title: "Bewerken",
  bgColor: "orange",
  onclick: "edit",
});

let btnDelete = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "trash",
  title: "Verwijderen",
  bgColor: "red",
  modal: "delete",
  onclick: "delete",
});

Component.addActionButton(btnAdd, btnEdit, btnDelete);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnEdit, "==1");
  Table.GetInstance(pageId).attachButton(btnDelete, ">0");
});
