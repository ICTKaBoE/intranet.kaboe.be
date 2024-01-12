import Form from "../../shared/default/js/object/Form.js";
import Button from "../../shared/default/js/object/Button.js";
import Helpers from "../../shared/default/js/object/Helpers.js";

window.checkAllLoadedCallbackPage = () => {
	Form.INSTANCES[`frm${pageId}`].attachButton(Button.INSTANCES['btn-submit']);
};

Helpers.CheckAllLoaded(window.checkAllLoadedCallbackPage);