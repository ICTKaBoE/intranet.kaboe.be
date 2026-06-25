import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

Component.addActionButton(btnFilter);
Component.addExtraPageInfo(`Laatste sync: ${lastSyncTime}`);

$(document).ready(() => {});
