import Button from "../../../shared/default/js/object/Button.js";

Button.GetInstance("btnResetLastNumber").setOnClick(() => {
	document.getElementById("lastNumber").value = 0;
});
