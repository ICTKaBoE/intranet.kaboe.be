export default class Table {
	static INSTANCES = {};

	constructor(element) {
		this.table = element;
		this.id = this.table.id || false;

		this.source = this.table.dataset.source || false;
		this.info = this.table.hasAttribute("data-info");
		this.paging = this.table.hasAttribute("data-paging");
		this.searching = this.table.hasAttribute("data-searching");

		this.init();
	}

	static ScanAndCreate() {
		$("table").each((ids, el) => {
			Table.INSTANCES[el.id] = new Table(el);
		});
	}

	init = () => {
		this.createOptions();
		this.createColumnOptions();
		this.createTable();
	};

	createOptions = () => {
		this.options = {};

		if (this.source) {
			this.options.ajax = {
				url: this.source,
				dataSrc: 'rows'
			};
		}

		this.options.info = this.info;
		this.options.paging = this.paging;
		this.options.searching = this.searching;
	};

	createColumnOptions = () => {
		let columns = $(this.table).find("th");
		console.log(columns);
	};

	createTable = () => {
		this.datatable = new DataTable(this.table, this.options);
	};
}