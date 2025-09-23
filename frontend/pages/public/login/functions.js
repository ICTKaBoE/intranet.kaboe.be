import Form from "../../../shared/default/js/object/Form.js";

window.localLogin = () => {
	Form.GetInstance(pageId).show();
	document.getElementById("username").focus();
};
