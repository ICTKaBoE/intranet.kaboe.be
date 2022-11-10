export default class Document {
	static toggleWait = () => {
		this.toggleModal('wait');
	};

	static toggleModal = (id) => {
		let modal = new bootstrap.Modal(`#modal-${id}`);
		modal.toggle();
	};
}