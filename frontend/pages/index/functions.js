import Form from "../../shared/default/js/object/Form.js";
import Button from "../../shared/default/js/object/Button.js";
import Helpers from "../../shared/default/js/object/Helpers.js";

window.checkAllLoadedCallbackPage = () => {
	Form.GetInstance(pageId).attachButton(Button.GetInstance("btn-submit"));
};

Helpers.CheckAllLoaded(window.checkAllLoadedCallbackPage);
