import Button from "../../../../shared/default/js/object/Button.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

Component.addActionButton(btnFilter);
