import Helpers from "../../../../shared/ui/js/custom/objects/Helpers.js";

let iconSearch = "https://t1.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=<URL>&size=256";

let url = document.getElementById("url");
let icon = document.getElementById("icon");
let iconPreview = document.getElementById("iconPreview");

url.onkeyup = (e) => {
	if (Helpers.isValidUrl(url.value)) {
		let urlIconSearch = iconSearch.replace("<URL>", url.value);
		icon.value = urlIconSearch;
		iconPreview.src = urlIconSearch;
	} else {
		icon.value = "";
		iconPreview.src = "";
	}
};