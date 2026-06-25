import Select from "../../../../../shared/default/js/object/Select.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";

window.anonymousView = (info) => {
  if (Boolean(Checkbox.GetInstance("Anonymous").getValue()))
    Select.GetInstance("victimId").disable();
  else Select.GetInstance("victimId").enable();
};

window.formView = (info) => {
  let val = Select.GetInstance("form").getValue();
  val = val.split(";").map((i) => parseInt(i));

  if (val.includes(window.SELECT_OTHER_ID))
    document.getElementById("form-O").classList.remove("d-none");
  else document.getElementById("form-O").classList.add("d-none");
};

window.outView = (info) => {
  let val = Select.GetInstance("out").getValue();
  val = val.split(";").map((i) => parseInt(i));

  if (val.includes(window.SELECT_OTHER_ID))
    document.getElementById("out-O").classList.remove("d-none");
  else document.getElementById("out-O").classList.add("d-none");
};

window.intentionView = (info) => {
  let val = Select.GetInstance("intention").getValue();
  val = val.split(";").map((i) => parseInt(i));

  if (val.includes(window.SELECT_OTHER_ID))
    document.getElementById("intention-O").classList.remove("d-none");
  else document.getElementById("intention-O").classList.add("d-none");
};

window.causeView = (info) => {
  let val = Select.GetInstance("cause").getValue();
  val = val.split(";").map((i) => parseInt(i));

  if (val.includes(window.SELECT_OTHER_ID))
    document.getElementById("cause-O").classList.remove("d-none");
  else document.getElementById("cause-O").classList.add("d-none");
};

let btnPrevStep = new Button(document.getElementById("btnPrevStep"), {
  type: Button.TYPE_ICON,
  icon: "chevron-left",
  title: "Vorige stap",
  bgColor: "primary",
  onclick: () => {
    Form.GetInstance(pageId).submit(true, "-");
  },
});

let btnNextStep = new Button(document.getElementById("btnNextStep"), {
  type: Button.TYPE_ICON,
  icon: "chevron-right",
  title: "Volgende stap",
  bgColor: "primary",
  classes: ["ms-auto"],
  onclick: () => {
    console.log(Button.INSTANCES["Button"]);
    Form.GetInstance(pageId).submit(true, "+");
  },
});

let btnSubmit = new Button(document.getElementById("btnSubmit"), {
  type: Button.TYPE_TEXT,
  text: "Opslaan",
  bgColor: "primary",
  classes: ["ms-auto"],
  onclick: () => {
    Form.GetInstance(pageId).submit();
  },
});

btnPrevStep.hide();
btnSubmit.hide();

$(document).ready(() => {
  Helpers.CheckAllLoaded(() => {
    setTimeout(() => {
      window.anonymousView();
    }, 500);
  }, [Form, Select]);
});
