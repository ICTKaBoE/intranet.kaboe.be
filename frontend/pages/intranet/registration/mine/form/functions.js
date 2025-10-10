import Button from "../../../../../shared/default/js/object/Button.js";
import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Component from "../../../../../shared/default/js/object/Component.js";
import DatePicker from "../../../../../shared/default/js/object/DatePicker.js";
import Form from "../../../../../shared/default/js/object/Form.js";
import Select from "../../../../../shared/default/js/object/Select.js";

window.setSchoolyear = () => {
	let sy = Select.GetInstance("schoolyearId").getValue();
	Select.GetInstance("studyyearId").setExtraLoadParam("schoolyearId", sy);
	Select.GetInstance("fieldId").setExtraLoadParam("schoolyearId", sy);
	Select.GetInstance("optionId").setExtraLoadParam("schoolyearId", sy);
	Select.GetInstance("talentId").setExtraLoadParam("schoolyearId", sy);
	Select.GetInstance("clilId").setExtraLoadParam("schoolyearId", sy);

	if (Select.GetInstance("schoolyearId").getItemDetails().current == true) {
		DatePicker.GetInstance("startAt").reload();
		document.getElementById("fsCurrentSchool").classList.remove("d-none");
	} else {
		DatePicker.GetInstance("startAt").setDate(
			Select.GetInstance("schoolyearId").getItemDetails().startAt
		);
		document.getElementById("fsCurrentSchool").classList.add("d-none");
	}
};

window.setField = () => {
	let v = Select.GetInstance("fieldId").getValue();
	Select.GetInstance("optionId").setExtraLoadParam("fieldId", v);
	Select.GetInstance("talentId").setExtraLoadParam("fieldId", v);
	Select.GetInstance("clilId").setExtraLoadParam("fieldId", v);

	Select.GetInstance("optionId").reload();
	Select.GetInstance("talentId").reload();
	Select.GetInstance("clilId").reload();
};

window.checkInsz = () => {
	if (Boolean(Checkbox.GetInstance("HasInsz").getValue()))
		document.getElementById("insz").removeAttribute("disabled");
	else document.getElementById("insz").setAttribute("disabled", "");
};

window.checkLivingWith = () => {
	if (Select.GetInstance("livingWith").getValue() == "O")
		document.getElementById("divLivingWithO").classList.remove("d-none");
	else document.getElementById("divLivingWithO").classList.add("d-none");

	if (Select.GetInstance("livingWith").getValue() == "I")
		document.getElementById("divLivingWithI").classList.remove("d-none");
	else document.getElementById("divLivingWithI").classList.add("d-none");
};

window.checkRelation1 = () => window.checkRelation(1);
window.checkRelation2 = () => window.checkRelation(2);
window.checkRelation3 = () => window.checkRelation(3);
window.checkRelation4 = () => window.checkRelation(4);
window.checkRelation5 = () => window.checkRelation(5);
window.checkRelation6 = () => window.checkRelation(6);

window.checkRelation = (pos) => {
	if (Select.GetInstance(`relation${pos}`).getValue() == "O")
		document
			.getElementById(`relationOther${pos}`)
			.removeAttribute("disabled");
	else
		document
			.getElementById(`relationOther${pos}`)
			.setAttribute("disabled", null);
};

window.checkRelations = () => {
	let relation1 = Select.GetInstance("relation1").getItemDetails();
	let relation2 = Select.GetInstance("relation2").getItemDetails();
	let relation3 = Select.GetInstance("relation3").getItemDetails();
	let relation4 = Select.GetInstance("relation4").getItemDetails();
	let relation5 = Select.GetInstance("relation5").getItemDetails();
	let relation6 = Select.GetInstance("relation6").getItemDetails();

	let rel1Text =
		relation1?.value == "O"
			? document.getElementById("relationOther1").value
			: relation1?.name;

	let rel2Text =
		relation2?.value == "O"
			? document.getElementById("relationOther2").value
			: relation2?.name;

	let rel3Text =
		relation3?.value == "O"
			? document.getElementById("relationOther3").value
			: relation3?.name;

	let rel4Text =
		relation4?.value == "O"
			? document.getElementById("relationOther4").value
			: relation4?.name;

	let rel5Text =
		relation5?.value == "O"
			? document.getElementById("relationOther5").value
			: relation5?.name;

	let rel6Text =
		relation6?.value == "O"
			? document.getElementById("relationOther6").value
			: relation6?.name;

	if (Boolean(Checkbox.GetInstance("AddressOpen1").getValue()) == true) {
		document.getElementById("fs-address1").classList.remove("d-none");
		document.getElementById("relationValue1").innerText = rel1Text;
		Select.GetInstance("copyFrom2").addOption(1, rel1Text);
		Select.GetInstance("copyFrom3").addOption(1, rel1Text);
		Select.GetInstance("copyFrom4").addOption(1, rel1Text);
		Select.GetInstance("copyFrom5").addOption(1, rel1Text);
		Select.GetInstance("copyFrom6").addOption(1, rel1Text);
	} else document.getElementById("fs-address1").classList.add("d-none");

	if (Boolean(Checkbox.GetInstance("AddressOpen2").getValue()) == true) {
		document.getElementById("fs-address2").classList.remove("d-none");
		document.getElementById("relationValue2").innerText = rel2Text;
		Select.GetInstance("copyFrom1").addOption(2, rel2Text);
		Select.GetInstance("copyFrom3").addOption(2, rel2Text);
		Select.GetInstance("copyFrom4").addOption(2, rel2Text);
		Select.GetInstance("copyFrom5").addOption(2, rel2Text);
		Select.GetInstance("copyFrom6").addOption(2, rel2Text);
	} else document.getElementById("fs-address2").classList.add("d-none");

	if (Boolean(Checkbox.GetInstance("AddressOpen3").getValue()) == true) {
		document.getElementById("fs-address3").classList.remove("d-none");
		document.getElementById("relationValue3").innerText = rel3Text;
		Select.GetInstance("copyFrom1").addOption(3, rel3Text);
		Select.GetInstance("copyFrom2").addOption(3, rel3Text);
		Select.GetInstance("copyFrom4").addOption(3, rel3Text);
		Select.GetInstance("copyFrom5").addOption(3, rel3Text);
		Select.GetInstance("copyFrom6").addOption(3, rel3Text);
	} else document.getElementById("fs-address3").classList.add("d-none");

	if (Boolean(Checkbox.GetInstance("AddressOpen4").getValue()) == true) {
		document.getElementById("fs-address4").classList.remove("d-none");
		document.getElementById("relationValue4").innerText = rel4Text;
		Select.GetInstance("copyFrom1").addOption(4, rel4Text);
		Select.GetInstance("copyFrom2").addOption(4, rel4Text);
		Select.GetInstance("copyFrom3").addOption(4, rel4Text);
		Select.GetInstance("copyFrom5").addOption(4, rel4Text);
		Select.GetInstance("copyFrom6").addOption(4, rel4Text);
	} else document.getElementById("fs-address4").classList.add("d-none");

	if (Boolean(Checkbox.GetInstance("AddressOpen5").getValue()) == true) {
		document.getElementById("fs-address5").classList.remove("d-none");
		document.getElementById("relationValue5").innerText = rel5Text;
		Select.GetInstance("copyFrom1").addOption(5, rel5Text);
		Select.GetInstance("copyFrom2").addOption(5, rel5Text);
		Select.GetInstance("copyFrom3").addOption(5, rel5Text);
		Select.GetInstance("copyFrom4").addOption(5, rel5Text);
		Select.GetInstance("copyFrom6").addOption(5, rel5Text);
	} else document.getElementById("fs-address5").classList.add("d-none");

	if (Boolean(Checkbox.GetInstance("AddressOpen6").getValue()) == true) {
		document.getElementById("fs-address6").classList.remove("d-none");
		document.getElementById("relationValue6").innerText = rel6Text;
		Select.GetInstance("copyFrom1").addOption(6, rel6Text);
		Select.GetInstance("copyFrom2").addOption(6, rel6Text);
		Select.GetInstance("copyFrom3").addOption(6, rel6Text);
		Select.GetInstance("copyFrom4").addOption(6, rel6Text);
		Select.GetInstance("copyFrom5").addOption(6, rel6Text);
	} else document.getElementById("fs-address6").classList.add("d-none");
};

window.copy1 = () =>
	window.copyTo(1, Select.GetInstance("copyFrom1").getValue());
window.copy2 = () =>
	window.copyTo(2, Select.GetInstance("copyFrom2").getValue());
window.copy3 = () =>
	window.copyTo(3, Select.GetInstance("copyFrom3").getValue());
window.copy4 = () =>
	window.copyTo(4, Select.GetInstance("copyFrom4").getValue());
window.copy5 = () =>
	window.copyTo(5, Select.GetInstance("copyFrom5").getValue());
window.copy6 = () =>
	window.copyTo(6, Select.GetInstance("copyFrom6").getValue());

window.copyTo = (to, from) => {
	if (from == "_e_") {
		document.getElementById(`street${to}`).value = "";
		document.getElementById(`number${to}`).value = "";
		document.getElementById(`bus${to}`).value = "";
		document.getElementById(`zipcode${to}`).value = "";
		document.getElementById(`city${to}`).value = "";
		Select.GetInstance(`country${to}`).clear();
	} else if (from == "_d_") {
		document.getElementById(`street${to}`).value =
			document.getElementById("addressStreet").value;
		document.getElementById(`number${to}`).value =
			document.getElementById("addressNumber").value;
		document.getElementById(`bus${to}`).value =
			document.getElementById("addressBus").value;
		document.getElementById(`zipcode${to}`).value =
			document.getElementById("addressZipcode").value;
		document.getElementById(`city${to}`).value =
			document.getElementById("addressCity").value;
		Select.GetInstance(`country${to}`).setValue(
			Select.GetInstance("addressCountryId").getValue()
		);
	} else {
		document.getElementById(`street${to}`).value = document.getElementById(
			`street${from}`
		).value;
		document.getElementById(`number${to}`).value = document.getElementById(
			`number${from}`
		).value;
		document.getElementById(`bus${to}`).value = document.getElementById(
			`bus${from}`
		).value;
		document.getElementById(`zipcode${to}`).value = document.getElementById(
			`zipcode${from}`
		).value;
		document.getElementById(`city${to}`).value = document.getElementById(
			`city${from}`
		).value;
		Select.GetInstance(`country${to}`).setValue(
			Select.GetInstance(`country${from}`).getValue()
		);
	}
};

window.checkLastSchool = () => {
	$("fieldset[id^='lastSchool']").addClass("d-none");
	$(
		`fieldset[id='lastSchool${Select.GetInstance(
			"lastSchool"
		).getValue()}']`
	).removeClass("d-none");
};

window.checkPictures = () => {
	let pic = Boolean(Checkbox.GetInstance("Picture").getValue());
	let cls = Boolean(Checkbox.GetInstance("ClassPicture").getValue());

	if (pic || cls)
		document.getElementById("fsPictures").classList.remove("d-none");
	else document.getElementById("fsPictures").classList.add("d-none");
};

window.checkProblemLearn = () => {
	if (Boolean(Checkbox.GetInstance("ProblemLearn").getValue()))
		document.getElementById("fsProblemLearn").classList.remove("d-none");
	else document.getElementById("fsProblemLearn").classList.add("d-none");
};

window.checkProblemLearnProblem = () => {
	if (Select.GetInstance("problemLearnProblem").getValue() == "O")
		document
			.getElementById("problemLearnProblemOther")
			.removeAttribute("disabled");
	else
		document
			.getElementById("problemLearnProblemOther")
			.setAttribute("disabled", null);
};

window.checkProblemHealthMedication = () => {
	if (Boolean(Checkbox.GetInstance("ProblemHealthMedication").getValue()))
		document
			.getElementById("problemHealthMedicationWhat")
			.removeAttribute("disabled");
	else
		document
			.getElementById("problemHealthMedicationWhat")
			.setAttribute("disabled", null);
};

window.checkProblemLearnCertificate = () => {
	if (Boolean(Checkbox.GetInstance("ProblemLearnCertificate").getValue()))
		document
			.getElementById("problemLearnCertificateReceivedContainer")
			.classList.remove("d-none");
	else
		document
			.getElementById("problemLearnCertificateReceivedContainer")
			.classList.add("d-none");
};

window.checkProblemFamily = () => {
	if (Boolean(Checkbox.GetInstance("ProblemFamily").getValue()))
		document.getElementById("fsProblemFamily").classList.remove("d-none");
	else document.getElementById("fsProblemFamily").classList.add("d-none");
};

window.checkProblemHealth = () => {
	if (Boolean(Checkbox.GetInstance("ProblemHealth").getValue()))
		document.getElementById("fsProblemHealth").classList.remove("d-none");
	else document.getElementById("fsProblemHealth").classList.add("d-none");
};

window.fillOverview = () => {
	let container = document.getElementById("overviewContainer");
	container.innerHTML = "";

	$(Form.GetInstance(pageId).element)
		.find("fieldset.form-fieldset")
		.each((i, fs) => {
			if (fs.classList.contains("d-none")) return;

			let _fs = document.createElement("fieldset");
			_fs.classList.add(...fs.classList.values());

			let _legend = document.createElement("legend");
			_legend.innerHTML = `${$(fs).parent()[0].dataset.step}: ${
				$(fs).parent()[0].dataset.title
			} - ${$(fs).find("legend").html()}`;
			_fs.appendChild(_legend);

			$(fs)
				.find(".form-label, .form-check")
				.each((j, l) => {
					let _row = document.createElement("div");
					_row.classList.add("row", "mb-1", "border-bottom-wide");

					let _strong = document.createElement("strong");
					_strong.classList.add("col-lg-4", "col-12");
					_strong.innerHTML = l.classList.contains("form-check")
						? $(l).find(".form-check-label")[0].innerHTML
						: l.innerHTML;
					_row.appendChild(_strong);

					let _div = document.createElement("div");
					_div.classList.add("col");

					if (l.classList.contains("form-check")) {
						if (l.parentElement.role == "checkbox")
							_div.innerHTML = Checkbox.GetInstance(
								l.parentElement.id
							).getValue()
								? "Ja"
								: "Nee";
					} else {
						if (l.nextElementSibling.tagName == "SELECT") {
							_div.innerHTML = Select.GetInstance(
								l.nextElementSibling.id
							).getText();
						} else _div.innerHTML = l.nextElementSibling.value;
					}

					_row.appendChild(_div);

					_fs.appendChild(_row);
				});

			container.appendChild(_fs);
		});

	// Object.keys(data).forEach((key) => {
	// 	if ((el = document.getElementById(`ov_${key}`))) {
	// 		if (el.dataset?.from == "select") {
	// 			let details = Select.GetInstance(key).getItemDetails();

	// 			el.innerHTML = details
	// 				? details[el.dataset?.fromAttribute || "text"]
	// 				: "";
	// 		} else if (el.dataset?.from == "check") {
	// 			el.innerHTML = Checkbox.GetInstance(key).getValue()
	// 				? el.dataset?.ifTrue
	// 				: el.dataset?.ifFalse;
	// 		} else el.innerHTML = data[key];
	// 	}
	// });
};

let btnReadEID = new Button({
	options: {
		type: Button.TYPE_ICON_TEXT,
		icon: "id",
		text: "Lees eID In",
		bgColor: "blue",
		onclick: () => {
			window.location.href = "../../read_eid.php";
		},
	},
});

let btnAddContact = new Button({
	element: document.getElementById("btnAddContact"),
});
btnAddContact.setOnClick(() => {
	let maxContacts = $("[data-contact]").length;
	let openContacts = $("[data-contact]").not(".d-none").length;

	if (openContacts == maxContacts - 1) btnAddContact.hide();
	Checkbox.GetInstance(`contactOpen${openContacts + 1}`).setValue(true);
	Checkbox.GetInstance(`addressOpen${openContacts + 1}`).setValue(true);
	$(`[data-contact='${openContacts + 1}']`).removeClass("d-none");
});

Component.addActionButton(btnReadEID);

$(document).ready(() => {
	Button.GetInstance("PrevStep").setOnClick(() => {
		Form.GetInstance(pageId).submit(true, "-");
	});

	Button.GetInstance("NextStep").setOnClick(() => {
		Form.GetInstance(pageId).submit(true, "+");
	});

	Button.GetInstance("Overview").setOnClick(() => {
		window.fillOverview();
		Form.GetInstance(pageId).submit(true, "+");
	});

	$("#name").on("change", () => {
		if ($("#name").val() == "Lampaert") {
			$("#firstName").val("Jano");
			$("#sex").val("M");
			$("#birthDate").val("1999-03-06");
			$("#birthPlace").val("Eeklo");
			$("#birthCountryId").val(237).change();
			$("#nationalityId").val(17).change();
			$("#insz").val("99.03.09-315.60");
			$("#phone").val("+32 497/70.39.22");
			$("#email").val("jano.lampaert@gmail.com");
			$("#addressStreet").val("Kerkstraat");
			$("#addressNumber").val(235);
			$("#addressZipcode").val(9050);
			$("#addressCity").val("Gentbrugge");
			$("#addressCountryId").val(237).change();
			$("#subscriberRelation").val("P").change();
			$("#meansOfTransport").val("B").change();
		}
	});
});
