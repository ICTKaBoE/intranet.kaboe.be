import Select from "../../../../../shared/default/js/object/Select.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Form from "../../../../../shared/default/js/object/Form.js";

window.virtualView = (info) => {
  let val = Checkbox.GetInstance("chbVirtual").getValue();

  if (val) {
    Select.GetInstance("parentSchoolId").disable();
    document.getElementById("parentSchool-warning").classList.add("d-none");
  } else {
    Select.GetInstance("parentSchoolId").enable();
    document.getElementById("parentSchool-warning").classList.remove("d-none");
  }
};

window.syncEmployeeADView = () => {
  let val = Checkbox.GetInstance("chbSyncEmployee").getValue();

  if (val) {
    document.getElementById("tab-ad-employee-item").classList.remove("d-none");
    document.getElementById("tab-ad-employee").classList.remove("d-none");
  } else {
    document.getElementById("tab-ad-employee-item").classList.add("d-none");
    document.getElementById("tab-ad-employee").classList.add("d-none");
  }
};

window.syncStudentADView = () => {
  let val = Checkbox.GetInstance("chbSyncStudent").getValue();

  if (val) {
    document.getElementById("tab-ad-student-item").classList.remove("d-none");
    document.getElementById("tab-ad-student").classList.remove("d-none");
  } else {
    document.getElementById("tab-ad-student-item").classList.add("d-none");
    document.getElementById("tab-ad-student").classList.add("d-none");
  }
};

$(document).ready(() => {
  setTimeout(() => {
    Helpers.CheckAllLoaded(() => {
      window.virtualView();
      window.syncEmployeeADView();
      window.syncStudentADView();
    }, [Form]);
  }, 500);
});
