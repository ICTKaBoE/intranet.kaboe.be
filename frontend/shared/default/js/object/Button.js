export default class Button {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
	}

	static ScanAndCreate() {
		$("button,.btn*").each((ids, el) => {
			Button.INSTANCES[el.id] = new Button(el);
		});
	}
}