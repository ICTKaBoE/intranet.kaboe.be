import Button from "../../../../shared/default/js/object/Button.js";
import Table from "../../../../shared/default/js/object/Table.js";
import Component from "../../../../shared/default/js/object/Component.js";

window.batteryStatus = () => {
  let selected = Table.GetInstance(pageId).getSelectedRowData();

  Table.GetInstance(pageId + "Battery").appendSource(
    selected[0].guid || selected[0].id,
  );

  Table.GetInstance(pageId + "Battery").reload();
};

// window.usage = () => {
// 	let selected = Table.GetInstance(pageId).getSelectedRowData();

// 	Table.GetInstance(pageId + "Usage").appendSource(
// 		selected[0].guid || selected[0].id
// 	);

// 	Table.GetInstance(pageId + "Usage").reload();
// };

// window.usageChildRowFormat = (d) => {
// 	let ret = "<ul>";

// 	d.logon.forEach((l) => {
// 		ret += `<li>${l.username} - ${l.formatted.logon}${
// 			l.logoff ? " - " + l.formatted.logoff : ""
// 		}</li>`;
// 	});

// 	ret += "</ul>";

// 	return ret;
// };

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

let btnBattery = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "battery-1",
  title: "Batterijstatus",
  bgColor: "blue",
  onclick: "batteryStatus",
  modal: "battery",
});

// let btnUsage = new Button({
// 	options: {
// 		type: Button.TYPE_ICON,
// 		icon: "clock",
// 		title: "Gebruik",
// 		bgColor: "blue",
// 		onclick: "usage",
// 		modal: "usage",
// 	},
// });

Component.addActionButton(btnFilter, btnBattery);
Component.addExtraPageInfo(`Laatste sync: ${lastSyncTime}`);

$(document).ready(() => {
  Table.GetInstance(pageId).attachButton(btnBattery, "==1");
  // Table.GetInstance(pageId).attachButton(btnUsage, "==1");
});
