export default class TinyMCE {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.init();
	}

	static ScanAndCreate = () => {
		$("[role='tinymce']").each((ids, el) => {
			TinyMCE.INSTANCES[el.id] = new TinyMCE(el);
		});
	};

	init = () => {
		let options = {
			selector: `#${this.id}`,
			height: 300,
			menubar: false,
			statusbar: false,
			plugins: [
				'advlist autolink lists link image charmap print preview anchor',
				'searchreplace visualblocks code fullscreen',
				'insertdatetime media table paste code help wordcount'
			],
			toolbar: 'undo redo | formatselect | ' +
				'bold italic backcolor | alignleft aligncenter ' +
				'alignright alignjustify | bullist numlist outdent indent | ' +
				'removeformat',
			content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
		};

		tinyMCE.init(options);
	};

	setValue = (value) => {
		tinyMCE.get(this.id).setContent(value);
	};
}