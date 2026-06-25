import Button from "../../../../../shared/default/js/object/Button.js";
import Component from "../../../../../shared/default/js/object/Component.js";

window.renderOptgroupItem = (data, escape) => {
  return `<div>${data?.formatted?.nameWithSchool || data.name}</div>`;
};

let btnReadEID = new Button(null, {
  type: Button.TYPE_ICON_TEXT,
  icon: "id",
  text: "Lees eID In",
  bgColor: "blue",
  onclick: () => {
    window.location.href = "../../read_eid.php";
  },
});

Component.addActionButton(btnReadEID);
