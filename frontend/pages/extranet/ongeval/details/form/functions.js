import Select from "../../../../../shared/default/js/object/Select.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Button from "../../../../../shared/default/js/object/Button.js";
import Form from "../../../../../shared/default/js/object/Form.js";

let btnDeny = new Button({
	element: document.getElementById("btnClose"),
	options: {
		onclick: () => {
			if (confirm("BEVESTIGING: Sluiten zonder gevolg")) {
				document.getElementById("status").value = "CNF";

				setTimeout(() => {
					Form.GetInstance(pageId).submit();
				}, 500);
			}
		},
	},
});

Helpers.CheckAllLoaded(() => {
	setTimeout(() => {
		Select.GetInstance("informatStudentRelationId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentRelationId").reload();

		Select.GetInstance("informatStudentEmailId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentEmailId").reload();

		Select.GetInstance("informatStudentNumberId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentNumberId").reload();

		Select.GetInstance("informatStudentBankId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentBankId").reload();

		Select.GetInstance("informatStudentAddressId").setExtraLoadParam(
			"informatStudentId",
			document.getElementById("informatStudentId").value
		);
		Select.GetInstance("informatStudentAddressId").reload();
	}, 500);
});
