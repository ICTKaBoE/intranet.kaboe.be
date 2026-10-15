import Select from "../../../../../shared/default/js/object/Select.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";

window.schoolIdView = (info) => {
  let val = Select.GetInstance("schoolId").getValue();
  Select.GetInstance("informatClassId").setExtraLoadParam("schoolId", val);
  Select.GetInstance("managementRoomId").setExtraLoadParam("schoolId", val);

  Select.GetInstance("informatClassId").reload();
  Select.GetInstance("managementRoomId").reload();
};
