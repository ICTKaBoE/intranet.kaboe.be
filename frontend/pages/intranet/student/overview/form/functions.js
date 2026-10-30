import Button from "../../../../../shared/default/js/object/Button.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Table from "../../../../../shared/default/js/object/Table.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.resetPasswordMain = () => {
  let val = Checkbox.GetInstance("main").getValue();

  if (val) {
    Checkbox.GetInstance("MainForceChangePasswordNextLogin").show();
    Checkbox.GetInstance("MainPrintDocument").show();
  } else {
    Checkbox.GetInstance("MainForceChangePasswordNextLogin").hide();
    Checkbox.GetInstance("MainPrintDocument").hide();
  }
};

let btnPrint = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "printer",
  title: "Printen",
  bgColor: "blue",
  modal: "printSelect",
  onclick: () => {
    Form.GetInstance(pageId + "PrintSelect").reset();
  },
});

let btnResetPassword = new Button(null, {
  type: Button.TYPE_ICON,
  icon: "lock",
  title: "Wachtwoord resetten",
  bgColor: "red",
  modal: "resetPassword",
  onclick: () => {
    Form.GetInstance(pageId + "ResetPasswordSingle").reset();
    Checkbox.GetInstance("MainForceChangePasswordNextLogin").hide();
    Checkbox.GetInstance("MainPrintDocument").hide();
  },
});

Component.addActionButton(btnPrint, btnResetPassword);
