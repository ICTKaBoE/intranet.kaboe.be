import Button from "./Button.js";

export default class Table {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this.autoRefresh = this.element.dataset.autoRefresh || false;

		this.data = null;
		this.extraData = {};
		this.buttons = {};

		this.tableOptions = {
			serverSide: false,
			responsive: true,
			pageLength: 25,
			layout: this.layout,
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
						features: ["info"],
					},
				],
				topEnd: [
					{
						className: "col-12 col-lg-6 pe-3",
						features: [
							{
								paging: {
									buttons: 3,
								},
							},
						],
					},
				],
				bottomStart: [
					{
						className: "col-12 col-lg-6 ps-3",
						features: ["info"],
					},
				],
				bottomEnd: [
					{
						className: "col-12 col-lg-6 pe-3 mb-2",
						features: [
							{
								paging: {
									buttons: 3,
								},
							},
						],
					},
				],
			},
		};

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
		if (!this.element.classList.contains("table-sm"))
			this.element.classList.add("table-sm");
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done((data) => {
			this.data = data;
		});
	};

	createDataTable = () => {
		this.tableOptions.columns = this.data.columns;
		this.tableOptions.data = this.data.rows;
		this.tableOptions.select = this.data.checkbox;

		if (this.data.checkbox) {
			this.tableOptions.columnDefs = [
				{
					orderable: false,
					render: DataTable.render.select(),
					targets: 0,
				},
			];
			this.tableOptions.order = this.data.defaultOrder || [[1, "asc"]];
		}

		this.datatable = $(this.element).DataTable(this.tableOptions);

		this.datatable.on("select deselect", () => this.checkButtonStates());
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
