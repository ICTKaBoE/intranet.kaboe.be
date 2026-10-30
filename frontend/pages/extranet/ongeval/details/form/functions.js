import Select from "../../../../../shared/default/js/object/Select.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Form from "../../../../../shared/default/js/object/Form.js";

let btnDeny = new Button(document.getElementById("btnClose"), {
  type: Button.TYPE_TEXT,
  text: "Sluiten zonder gevolg",
  bgColor: "danger",
  onclick: () => {
    if (confirm("BEVESTIGING: Sluiten zonder gevolg")) {
      document.getElementById("status").value = "CNF";

      setTimeout(() => {
        Form.GetInstance(pageId).submit();
      }, 500);
    }
  },
});

$(document).ready(() => {
  if (add == "") {
    Component.addActionButton(btnCancel, btnSave);

    Helpers.CheckAllLoaded(async () => {
      await Helpers.sleep(750);
      let informatStudentId =
        document.getElementById("informatStudentId").value;
      Select.GetInstance("informatStudentRelationId").setExtraLoadParam(
        "informatStudentId",
        informatStudentId,
      );

      Select.GetInstance("informatStudentEmailId").setExtraLoadParam(
        "informatStudentId",
        informatStudentId,
      );

      Select.GetInstance("informatStudentNumberId").setExtraLoadParam(
        "informatStudentId",
        informatStudentId,
      );

      Select.GetInstance("informatStudentBankId").setExtraLoadParam(
        "informatStudentId",
        informatStudentId,
      );

      Select.GetInstance("informatStudentAddressId").setExtraLoadParam(
        "informatStudentId",
        informatStudentId,
      );

      Select.GetInstance("informatStudentRelationId").reload();
      Select.GetInstance("informatStudentEmailId").reload();
      Select.GetInstance("informatStudentNumberId").reload();
      Select.GetInstance("informatStudentBankId").reload();
      Select.GetInstance("informatStudentAddressId").reload();
    }, [Form, Select]);
  }
});
