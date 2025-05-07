import List from "../../../shared/default/js/object/List.js";

window.addEventListener("popstate", function (event) {
	let folder = window.location.hash.substring(1);
	List.GetInstance("Home").setExtraLoadParam("folder", folder);
	List.GetInstance("Home").reload();
});
