import Calendar from "../../../shared/default/js/object/Calendar.js";
import Select from "../../../shared/default/js/object/Select.js";

window.loadCalender = () => {
    let _schoolId = Select.GetInstance("schoolId").getValue();
    let _type = Select.GetInstance("type").getValue();
    let _assetId = Select.GetInstance("assetId").getValue();

    Calendar.INSTANCES[`cal${pageId}`].addExtraData('schoolId', _schoolId);
    Calendar.INSTANCES[`cal${pageId}`].addExtraData('type', _type);
    Calendar.INSTANCES[`cal${pageId}`].addExtraData('assetId', _assetId);
    Calendar.ReloadAll();
}

window.check = () => {
    let _schoolId = Select.GetInstance("schoolId").getValue();
    let _type = Select.GetInstance("type").getValue();
    let _assetId = Select.GetInstance("assetId");

    _assetId.setExtraLoadParam("schoolId", _schoolId);
    if (_type == "L") {
        _assetId.enable();
        _assetId.setDetails("computer");
        _assetId.setExtraLoadParam("type", "L");
    } else if (_type == "D") {
        _assetId.enable();
        _assetId.setDetails("computer");
        _assetId.setExtraLoadParam("type", "D");
    } else if (_type == "I") {
        _assetId.enable();
        _assetId.setDetails("ipad");
        _assetId.setExtraLoadParam("type", "I");
    } else if (_type == "R") {
        _assetId.enable();
        _assetId.setDetails("room");
        _assetId.setExtraLoadParam("type", "R")
    } else if (_type == "LK") {
        _assetId.enable();
        _assetId.setDetails("laptopcart");
        _assetId.setExtraLoadParam("type", "L");
    } else if (_type == "IK") {
        _assetId.enable();
        _assetId.setDetails("ipadcart");
        _assetId.setExtraLoadParam("type", "I");
    }
}