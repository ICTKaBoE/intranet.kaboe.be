import Select from "../../../../shared/default/js/object/Select.js";

window.itemView = () => {
	let item = Select.GetInstance("itemId").getItemDetails();
	console.log(item);
	document.getElementById("valueContainer").innerHTML =
		item["formatted.valueHtml"];
};
