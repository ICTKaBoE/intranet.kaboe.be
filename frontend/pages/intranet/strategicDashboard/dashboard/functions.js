import Chart from "../../../../shared/default/js/object/Chart.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import List from "../../../../shared/default/js/object/List.js";
import Rating from "../../../../shared/default/js/object/Rating.js";

$(document).ready(() => {
	Helpers.CheckAllLoaded(() => {
		setTimeout(() => {
			[Rating, Chart].forEach((c) => c.ScanAndCreate());
		}, 250);
	}, [List]);
});
