import Button from "../../../../../shared/default/js/object/Button.js";
import Select from "../../../../../shared/default/js/object/Select.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import TinyMCE from "../../../../../shared/default/js/object/TinyMCE.js";

window.renderOptgroupItem = (data, escape) => {
  return `<div>${data["formatted.name"] || data["formatted.fullNameReversed"]}</div>`;
};

window.departmentView = () => {
  Select.GetInstance("informatStudentId").setExtraLoadParam(
    "schoolId",
    Select.GetInstance("schoolId").getValue(),
  );
  Select.GetInstance("informatStudentId").setExtraLoadParam(
    "departmentId",
    Select.GetInstance("departmentId").getValue(),
  );
  Select.GetInstance("informatStudentId").reload();
};

window.typeView = () => {
  let details = Select.GetInstance("typeId").getItemDetails();

  Select.GetInstance("momentId").setExtraLoadParam("typeId", details.id);

  if (details.courseDependsOnSkore) {
    Select.GetInstance("courseId").hide();
    Select.GetInstance("coursePerStudent").show();
    Select.GetInstance("momentId").removeExtraLoadParam("courseId");
    Select.GetInstance("momentId").reload();
  } else {
    Select.GetInstance("courseId").show();
    Select.GetInstance("coursePerStudent").hide();
    Select.GetInstance("momentId").setExtraLoadParam("courseId", 0);
    Select.GetInstance("momentId").reload();
  }

  if (details.allowWithoutDate) {
    Select.GetInstance("courseId").hide();
    Select.GetInstance("coursePerStudent").show();
    Select.GetInstance("momentId").hide();
  } else {
    Select.GetInstance("courseId").show();
    Select.GetInstance("coursePerStudent").hide();
    Select.GetInstance("momentId").show();
  }

  if (details.onComputer)
    document.getElementById("onComputer-Y").classList.remove("d-none");
  else document.getElementById("onComputer-Y").classList.add("d-none");
};

window.courseView = () => {
  if (!Select.GetInstance("typeId").getItemDetails().courseDependsOnSkore) {
    Select.GetInstance("momentId").setExtraLoadParam(
      "courseId",
      Select.GetInstance("courseId").getValue(),
    );

    Select.GetInstance("momentId").reload();
  }
};

window.studentView = () => {
  let informatData = Select.GetInstance("informatStudentId").getItemDetails();
  let studentId = informatData
    ? informatData.id !== undefined
      ? informatData.id
      : informatData.map((s) => s.id).join(";")
    : null;
  let classgroupId = informatData
    ? informatData["linked.class.id"] !== undefined
      ? informatData["linked.class.id"]
      : informatData.map((s) => s["linked.class.id"]).join(";")
    : null;

  Select.GetInstance("coursePerStudent").setExtraLoadParam(
    "informatStudentId",
    studentId,
  );

  Select.GetInstance("coursePerStudent").setExtraLoadParam(
    "informatClassgroupId",
    classgroupId,
  );

  document.getElementById("informatClassgroupId").value = classgroupId;
  Select.GetInstance("coursePerStudent").reload();
};

window.computerTypeView = () => {
  if (Select.GetInstance("computerType").getValue() == "O")
    document.getElementById("computerType-O").classList.remove("d-none");
  else document.getElementById("computerType-O").classList.add("d-none");
};

let btnCancel = new Button(document.getElementById("btnCancel"), {
  type: Button.TYPE_ICON_TEXT,
  icon: "x",
  text: "Annuleren",
  title: "Annuleren",
  bgColor: "red",
  onclick: () => {
    history.back();
  },
});

let btnSave = new Button(document.getElementById("btnSave"), {
  type: Button.TYPE_ICON_TEXT,
  icon: "check",
  text: "Opslaan",
  title: "Opslaan",
  bgColor: "success",
  onclick: () => {
    Form.GetInstance(pageId).submit();
  },
});
