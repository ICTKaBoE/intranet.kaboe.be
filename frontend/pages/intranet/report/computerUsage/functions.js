import Button from "../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Chart from "../../../../shared/default/js/object/Chart.js";

window.filter = () => {
  Chart.GetInstance(pageId + "Amount").addExtraData(
    "schoolId",
    Select.GetInstance("schoolId").getValue(),
  );
  Chart.GetInstance(pageId + "Amount").addExtraData(
    "schoolyear",
    document.getElementById("schoolyear").value,
  );
  Chart.GetInstance(pageId + "Amount").addExtraData(
    "type",
    Select.GetInstance("type").getValue(),
  );

  Chart.GetInstance(pageId + "Time").addExtraData(
    "schoolId",
    Select.GetInstance("schoolId").getValue(),
  );
  Chart.GetInstance(pageId + "Time").addExtraData(
    "schoolyear",
    document.getElementById("schoolyear").value,
  );
  Chart.GetInstance(pageId + "Time").addExtraData(
    "type",
    Select.GetInstance("type").getValue(),
  );

  Helpers.closeAllModals();
  Chart.GetInstance(pageId + "Amount").reload();
  Chart.GetInstance(pageId + "Time").reload();
};

window.formatAmount = (v) => {
  return Helpers.formatValue(v, "double", { precision: 2 });
};

window.formatTime = (v) => {
  return Helpers.formatValue(v, "secondsToDhms");
};

let btnFilter = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "filter",
  title: "Filteren",
  bgColor: "blue",
  modal: "filter",
});

Component.addActionButton(btnFilter);
