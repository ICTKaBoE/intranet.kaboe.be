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
  //   Table.GetInstance(pageId).reload();

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

  //   Table.GetInstance(pageId).reload();
};

window.studentView = () => {
  let informatData = Select.GetInstance("informatStudentId").getItemDetails();
  let studentId =
    informatData.id !== undefined
      ? informatData.id
      : informatData.map((s) => s.id).join(";");
  let classgroupId =
    informatData["linked.class.id"] !== undefined
      ? informatData["linked.class.id"]
      : informatData.map((s) => s["linked.class.id"]).join(";");

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

// let btnAdd = new Button({
//   element: document.getElementById("btnAdd"),
//   options: {
//     onclick: () => {
//       let student = Select.GetInstance("informatStudentId").getItemDetails();

//       let course = Select.GetInstance(
//         Select.GetInstance("typeId").getItemDetails().courseDependsOnSkore
//           ? "coursePerStudent"
//           : "courseId",
//       ).getItemDetails();

//       let moment = Select.GetInstance("momentId").getItemDetails();
//       //   console.log(moment.seats, moment.availableSeats);

//       let department = Select.GetInstance("departmentId").getItemDetails();
//       let schoolId = department["linked.school.virtual"]
//         ? department["linked.school.linked.parentSchool.id"]
//         : department.schoolId;

//       let data = {
//         schoolId: schoolId,
//         departmentId: department.id,
//         typeId: Select.GetInstance("typeId").getValue(),
//         courseId: course.id,
//         momentId: moment.id,
//         informatStudentId: student.id,
//         classgroupId: Select.GetInstance("classgroupId").getValue(),
//         remark: document.getElementById("remark").value,
//         fullNameReversed: student["formatted.fullNameReversed"],
//         className: Select.GetInstance("classgroupId").getItemDetails().name,
//         courseName: course.name,
//       };

//       //   Table.GetInstance(pageId).addRow(
//       //     data,
//       //     Select.GetInstance("typeId").getItemDetails().courseDependsOnSkore,
//       //     "fullNameReversed",
//       //   );
//     },
//   },
// });

// let btnRemove = new Button({
//   element: document.getElementById("btnRemove"),
//   options: {
//     onclick: () => {
//       //   Table.GetInstance(pageId).deleteSelectedRows();
//     },
//   },
// });

let btnCancel = new Button({
  element: document.getElementById("btnCancel"),
  options: {
    type: Button.TYPE_ICON_TEXT,
    icon: "x",
    text: "Annuleren",
    title: "Annuleren",
    bgColor: "red",
    onclick: () => {
      history.back();
    },
  },
});

let btnSave = new Button({
  element: document.getElementById("btnSave"),
  options: {
    type: Button.TYPE_ICON_TEXT,
    icon: "check",
    text: "Opslaan",
    title: "Opslaan",
    bgColor: "success",
    onclick: () => {
      Form.GetInstance(pageId).submit();
    },
  },
});

// let btnSave = new Button({
//   options: {
//     type: Button.TYPE_ICON_TEXT,
//     icon: "check",
//     text: "Opslaan",
//     title: "Opslaan",
//     bgColor: "primary",
//     onclick: () => {
//       //   Table.GetInstance(pageId).selectAllRows();
//       document.getElementById("postData").value = JSON
//         .stringify
//         // Table.GetInstance(pageId).getSelectedRowData(),
//         ();
//       Form.GetInstance(pageId).submit();
//       //   Table.GetInstance(pageId).deselectAllRows();
//     },
//   },
// });

// Component.addActionButton(btnCancel, btnSave);

$(document).ready(() => {
  //   Table.GetInstance(pageId).attachButton(btnRemove, ">=1");
});
