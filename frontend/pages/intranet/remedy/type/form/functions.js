import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";

window.checkAllowWithoutDate = () => {
  if (Checkbox.GetInstance("allowWithoutDate").getValue()) {
    Checkbox.GetInstance("manualAssignDate").disable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForThisTypeOnSameDay",
    ).disable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForSameCourseOnSameDay",
    ).disable();
  } else {
    Checkbox.GetInstance("manualAssignDate").enable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForThisTypeOnSameDay",
    ).enable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForSameCourseOnSameDay",
    ).enable();
  }
};

window.checkManualAssignDate = () => {
  if (Checkbox.GetInstance("manualAssignDate").getValue()) {
    Checkbox.GetInstance("allowWithoutDate").disable();
  } else {
    Checkbox.GetInstance("allowWithoutDate").enable();
  }
};

window.checkAllowMultipleRegistrationsForThisTypeOnSameDay = () => {
  if (
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForThisTypeOnSameDay",
    ).getValue()
  ) {
    Checkbox.GetInstance("allowWithoutDate").disable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForSameCourseOnSameDay",
    ).disable();
  } else {
    Checkbox.GetInstance("allowWithoutDate").enable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForSameCourseOnSameDay",
    ).enable();
  }
};

window.checkAllowMultipleRegistrationsForSameCourseOnSameDay = () => {
  if (
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForSameCourseOnSameDay",
    ).getValue()
  ) {
    Checkbox.GetInstance("allowWithoutDate").disable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForThisTypeOnSameDay",
    ).disable();
  } else {
    Checkbox.GetInstance("allowWithoutDate").enable();
    Checkbox.GetInstance(
      "allowMultipleRegistrationsForThisTypeOnSameDay",
    ).enable();
  }
};
