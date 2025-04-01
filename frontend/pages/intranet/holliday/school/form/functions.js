$(document).ready(() => {
	$("#fullDay").on("change", () => {
		if ($("#fullDay").is(":checked")) {
			document.getElementById("start").type = "date";
			document.getElementById("end").type = "date";
			document.getElementById("end").disabled = true;

			document.getElementById("start").onchange = () => {
				document.getElementById("end").value =
					document.getElementById("start").value;
			};
		} else {
			document.getElementById("start").type = "datetime-local";
			document.getElementById("end").type = "datetime-local";
			document.getElementById("end").disabled = false;
		}
	});
});
