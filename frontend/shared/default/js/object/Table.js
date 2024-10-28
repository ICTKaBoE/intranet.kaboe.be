import Button from "./Button.js";

export default class Table {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this._source = false;
		this.holdLoad = this.element.hasAttribute("data-hold-load");
		this.autoRefresh = this.element.dataset.autoRefresh || false;
		this.defaultExtraData = this.element.dataset.extra || false;
		this.doubleClickAction =
			this.element.dataset.doubleClickAction || false;
		this.small = this.element.hasAttribute("data-small");
		this.noSearch = this.element.hasAttribute("data-no-search");
		this.noInfo = this.element.hasAttribute("data-no-info");
		this.noPaging = this.element.hasAttribute("data-no-paging");
		this.childRowFormatFunction =
			this.element.dataset.childRowFormat || false;

		this.data = null;
		this.extraData = {};
		this.buttons = {};

		if (this.defaultExtraData) {
			let extraData = this.defaultExtraData
				.replace("[", "")
				.replace("]", "")
				.split("|");

			this.extraData = {};
			extraData.forEach((v) => {
				v = v.split("=");
				this.extraData[v[0]] = v[1];
			});
		}

		this.tableOptions = {
			columnDefs: [],
			serverSide: false,
			responsive: {
				details: {
					type: "inline",
				},
			},
			pageLength: 25,
			language: {
				url: "//cdn.datatables.net/plug-ins/2.1.5/i18n/nl-NL.json",
				select: {
					cells: null,
					columns: null,
					rows: null,
				},
				paginate: {
					first: '<i class="ti ti-chevrons-left"></i>',
					last: '<i class="ti ti-chevrons-right"></i>',
					previous: '<i class="ti ti-chevron-left"></i>',
					next: '<i class="ti ti-chevron-right"></i>',
				},
			},
			layout: {
				topStart: [
					{
						className: "col-12 col-lg-6 ps-3",
						features: {
							info: true,
						},
					},
				],
				topEnd: [
					{
						className: "col-12 col-lg-6 pe-3",
						features: {
							paging: {
								buttons: 5,
							},
						},
					},
				],
				bottomStart: [
					{
						className: "col-12 col-lg-6 ps-3",
						features: {
							info: true,
						},
					},
				],
				bottomEnd: [
					{
						className: "col-12 col-lg-6 pe-3 mb-2",
						features: {
							paging: {
								buttons: 5,
							},
						},
					},
				],
			},
		};

		if (this.noInfo) this.tableOptions.info = false;
		if (this.noPaging) this.tableOptions.paging = false;

		this.init();
	}

	static ScanAndCreate = () => {
		$("table[role='table']").each((ids, el) => {
			if (!Table.INSTANCES.hasOwnProperty(el.getAttribute("id")))
				Table.INSTANCES[el.getAttribute("id")] = new Table(el);
		});
	};

	static GetInstance = (id) => {
		if (!id.startsWith("tbl")) id = `tbl${id}`;
		return Table.INSTANCES[id] || false;
	};

	static ReloadAll = () => {
		for (const tbl in Table.INSTANCES) {
			Table.INSTANCES[tbl].reload();
		}
	};

	static SearchAll = (value) => {
		for (const tbl in Table.INSTANCES) {
			Table.INSTANCES[tbl].search(value);
		}
	};

	init = async () => {
		this.createStructure();

		await this.getData();
		this.createDataTable();
		this.checkButtonStates();

		if (this.autoRefresh) this.startAutoRefresh();
	};

	createStructure = () => {
		if (!this.element.classList.contains("table"))
			this.element.classList.add("table");
	};

	getData = () => {
		if (!this.source) return;

		return $.get(
			this.source + (this._source ? "/" + this._source : ""),
			this.extraData
		).done((data) => {
			this.data = data;
		});
	};

	createDataTable = () => {
		this.tableOptions.columns = this.data.columns;
		this.tableOptions.data = this.data.rows;
		this.tableOptions.select = this.data.checkbox;

		if (this.data.checkbox) {
			this.tableOptions.columnDefs.push({
				orderable: false,
				render: DataTable.render.select(),
				targets: 0,
			});
		}

		this.tableOptions.order = this.data.defaultOrder || [[1, "asc"]];
		this.datatable = $(this.element).DataTable(this.tableOptions);

		this.datatable.on("select deselect", () => this.checkButtonStates());

		if (this.doubleClickAction) {
			let dt = this.datatable;
			let action = this.doubleClickAction;
			this.datatable.on("dblclick", "tr", function (e) {
				dt.rows(this).select();
				window[action]();
			});
		}

		if (this.data.childRows) {
			let dt = this.datatable;
			let fn = this.childRowFormatFunction;
			this.datatable.on("requestChild", (e, row) => {
				row.child(window[fn](row.data())).show();
			});

			this.datatable.on("click", "tbody td.dt-control", function () {
				var tr = $(this).closest("tr");
				var row = dt.row(tr);

				if (row.child.isShown()) {
					// This row is already open - close it
					row.child.hide();
				} else {
					// Open this row
					row.child(window[fn](row.data())).show();
				}
			});
		}
	};

	attachButton = (button, showIf = null) => {
		if (!button instanceof Button) return;

		this.buttons[button.id] = {
			button: button,
			showIf: showIf,
		};
	};

	checkButtonStates = () => {
		let count = this.datatable.rows({ selected: true }).count();

		for (let btn in this.buttons) {
			if (Object.hasOwnProperty.call(this.buttons, btn)) {
				let button = this.buttons[btn];

				if (eval(count + button.showIf)) button.button.enable();
				else button.button.disable();
			}
		}
	};

	startAutoRefresh = () => {
		setInterval(() => {
			this.reload();
		}, this.autoRefresh * 1000);
	};

	reload = async () => {
		await this.getData();
		this.datatable.clear().rows.add(this.data.rows).draw();
	};

	search = (value) => {
		if (this.noSearch) return;
		this.datatable.search(value).draw();
	};

	addExtraData = (key, value) => {
		this.extraData[key] = value;
	};

	removeExtraData = (key) => {
		delete this.extraData[key];
	};

	clearExtraData = () => {
		this.extraData = {};
	};

	appendSource = (value) => {
		this._source = value;
	};

	getSelectedRowData = () => {
		let returnData = [];
		let count = this.datatable.rows({ selected: true }).count();
		let data = this.datatable.rows({ selected: true }).data();

		for (let i = 0; i < count; i++) {
			returnData.push(data[i]);
		}

		return returnData;
	};
}
