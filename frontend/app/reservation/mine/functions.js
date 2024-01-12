import Helpers from "../../../shared/default/js/object/Helpers.js";
import Form from "../../../shared/default/js/object/Form.js";
import Select from "../../../shared/default/js/object/Select.js";

window.add = (info) => {
    let data = info?.event ?? info;

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("create");

    Form.GetInstance(`frm${pageId}`).setField("startDate", data.startStr);
    Form.GetInstance(`frm${pageId}`).setField("startTime", data.startStr.split("T")[1].split("+")[0]);
    Form.GetInstance(`frm${pageId}`).setField("endDate", data.endStr);
    Form.GetInstance(`frm${pageId}`).setField("endTime", data.endStr.split("T")[1].split("+")[0]);

    Helpers.toggleModal("reservation");
};

window.edit = (info) => {
    let data = info?.event ?? info;

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("update");
    Form.GetInstance(`frm${pageId}`).prefillForm(data.id);
    Helpers.toggleModal("reservation");
};

window.delete = () => {
    let id = Form.GetInstance(`frm${pageId}`).lastLoadedId;
    if (!id) return;

    Form.GetInstance(`frm${pageId}`).reset();
    Form.GetInstance(`frm${pageId}`).setActiveType("delete");
    Form.GetInstance(`frm${pageId}`).setField("ids", id);
};

window.check = () => {
    let _type = Select.GetInstance("type").getValue();
    let _schoolId = Select.GetInstance("schoolId").getValue();
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
};