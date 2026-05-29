import Button from "../../../../shared/default/js/object/Button.js";
import Form from "../../../../shared/default/js/object/Form.js";
import Component from "../../../../shared/default/js/object/Component.js";
import Select from "../../../../shared/default/js/object/Select.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";

window.renderOptgroupItem = (data, escape) => {
  return `<div>${data?.formatted?.nameWithSchool || data.name}</div>`;
};

window.insertNew = (index, roleIdDefaultValue = "", toDefaultValue = "") => {
  let container = document.getElementById("new_filtered_container");
  let template = document
    .getElementById("new_filtered_<INDEX>")
    .cloneNode(true);
  template.classList.remove("d-none");

  template.id = template.id.replace("<INDEX>", index);
  template.innerHTML = template.innerHTML.replaceAll("<INDEX>", index);
  template.innerHTML = template.innerHTML.replace("data-no-create", "");
  template.innerHTML = template.innerHTML.replace("<ROLE_ID_DEFAULTVALUE>", roleIdDefaultValue);
  template.innerHTML = template.innerHTML.replace("<TO_DEFAULTVALUE>", toDefaultValue);

  container.appendChild(template);
  document.getElementById(`btnDeleteNew${index}`).onclick = () =>
    window.deleteNew(index);
  [Select].forEach((c) => c.ScanAndCreate());
};

window.insertEdit = (index, parameterDefaultValue = "", toDefaultValue = "") => {
  let container = document.getElementById("edit_filtered_container");
  let template = document
    .getElementById("edit_filtered_<INDEX>")
    .cloneNode(true);
  template.classList.remove("d-none");

  template.id = template.id.replace("<INDEX>", index);
  template.innerHTML = template.innerHTML.replaceAll("<INDEX>", index);
  template.innerHTML = template.innerHTML.replace("data-no-create", "");
  template.innerHTML = template.innerHTML.replace("<PARAMETER_DEFAULTVALUE>", parameterDefaultValue);
  template.innerHTML = template.innerHTML.replace("<TO_DEFAULTVALUE>", toDefaultValue);

  container.appendChild(template);
  document.getElementById(`btnDeleteEdit${index}`).onclick = () =>
    window.deleteEdit(index);
  [Select].forEach((c) => c.ScanAndCreate());
};

window.deleteNew = (index) => {
  document.getElementById(`new_filtered_${index}`).classList.add("d-none");
};

window.deleteEdit = (index) => {
  document.getElementById(`edit_filtered_${index}`).classList.add("d-none");
};

window.generateNewJson = () => {
  let json = [];
  let jsonElement = document.getElementById("flow.new.filtered");
  let amount =
    document.getElementById("new_filtered_container").children.length - 1;

  if (amount) {
    for (let a = 0; a < amount; a++) {
      if (
        document
          .getElementById(`new_filtered_${a}`)
          .classList.contains("d-none")
      )
        continue;

      json.push({
        roleId: Select.GetInstance(`flow_new_roleId_${a}`).getValue(),
        to: document.getElementById(`flow_new_to_${a}`).value,
      });
    }

    jsonElement.value = JSON.stringify(json);
  }
};

window.generateEditJson = () => {
  let json = [];
  let jsonElement = document.getElementById("flow.edit.filtered");
  let amount =
    document.getElementById("edit_filtered_container").children.length - 1;

  if (amount) {
    for (let a = 0; a < amount; a++) {
      if (
        document
          .getElementById(`edit_filtered_${a}`)
          .classList.contains("d-none")
      )
        continue;

      json.push({
        parameterId: Select.GetInstance(`flow_edit_parameter_${a}`).getValue(),
        to: document.getElementById(`flow_edit_to_${a}`).value,
      });
    }

    jsonElement.value = JSON.stringify(json);
  }
};

let btnSave = new Button({
  options: {
    type: Button.TYPE_ICON_TEXT,
    icon: "check",
    text: "Opslaan",
    title: "Opslaan",
    bgColor: "primary",
    onclick: () => {
      window.generateNewJson();
      window.generateEditJson();
      Form.GetInstance(pageId).submit();
    },
  },
});

let btnAddNew = new Button({
  element: document.getElementById("btnAddNew"),
  options: {
    type: Button.TYPE_ICON,
    icon: "plus",
    bgColor: "success",
    title: "Nieuwe filter toevoegen",
    onclick: () =>
      window.insertNew(
        document.getElementById("new_filtered_container").children.length - 1,
      ),
  },
});

let btnAddEdit = new Button({
  element: document.getElementById("btnAddEdit"),
  options: {
    type: Button.TYPE_ICON,
    icon: "plus",
    bgColor: "success",
    title: "Nieuwe filter toevoegen",
    onclick: () =>
      window.insertEdit(
        document.getElementById("edit_filtered_container").children.length - 1,
      ),
  },
});

Component.addActionButton(btnSave);

$(document).ready(() => {
  Helpers.CheckAllLoaded(() => {
    setTimeout(() => {
      let jsonNew = JSON.parse(document.getElementById("flow.new.filtered").value || "[]");
      let jsonEdit = JSON.parse(document.getElementById("flow.edit.filtered").value || "[]");

      if (jsonNew.length) jsonNew.forEach((item, index) => window.insertNew(index, item.roleId, item.to));
      if (jsonEdit.length) jsonEdit.forEach((item, index) => window.insertEdit(index, item.parameterId, item.to));
    }, 500);
  }, [Form]);
});
