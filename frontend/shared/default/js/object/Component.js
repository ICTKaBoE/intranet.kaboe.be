import Button from "./Button.js";

export default class Component {
	static addActionButton = (...buttons) => {
		for (let button of buttons)
			document
				.getElementById("action-buttons")
				.appendChild(button.write());
	};

	static addExtraPageInfo = (...infos) => {
		for (let info of infos) {
			let btn = new Button({
				options: {
					type: Button.TYPE_TEXT,
					bgColor: "outline-secondary",
					text: info,
				},
			});

			document.getElementById("extra-page-info").appendChild(btn.write());
		}
	};
}
