import Button from "../../../shared/ui/js/custom/objects/Button.js";
import Helpers from "../../../shared/ui/js/custom/objects/Helpers.js";
import TaskBoard from "../../../shared/ui/js/custom/objects/TaskBoard.js";

window.addRequest = () => {
	Helpers.redirect("/add");
};

window.setInProgress = (id) => {
	window.setOverall(id, 'inprogress');
};

window.setWaiting = (id) => {
	window.setOverall(id, 'waiting');
};

window.setCompleted = (id) => {
	window.setOverall(id, 'completed');
};

window.setOverall = (id, status) => {
	let tb = TaskBoard.INSTANCES[`tbr${pageId}`];

	if (tb.update) {
		Helpers.request({
			url: tb.update.replace('<id>', id).replace('<status>', status),
			method: "POST"
		}).then(() => {
			TaskBoard.INSTANCES[`tbr${pageId}`].reInit();
		});
	}
};

let btnAdd = new Button({
	type: "icon-text",
	text: "Toevoegen",
	icon: "plus",
	backgroundColor: "green",
	onclick: "addRequest"
});

Helpers.addButtonToPageTitle(btnAdd);