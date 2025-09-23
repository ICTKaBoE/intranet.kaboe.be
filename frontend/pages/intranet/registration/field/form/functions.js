import Select from "../../../../../shared/default/js/object/Select.js";

window.setSchoolyear = () => {
	let sy = Select.GetInstance("schoolyearId").getValue();
	Select.GetInstance("studyyearId").setExtraLoadParam("schoolyearId", sy);
};
