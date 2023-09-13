import Form from "../../shared/default/js/object/Form.js";
import Button from "../../shared/default/js/object/Button.js";

$(document).ready(() => {
	Form.INSTANCES[`frm${pageId}`].attachButton(Button.INSTANCES['btn-submit']);
});