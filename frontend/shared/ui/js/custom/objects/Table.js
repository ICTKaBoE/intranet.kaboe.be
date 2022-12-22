import Helpers from "./Helpers.js";

export default class Table {
	static INSTANCES = {};

	constructor(element) {
		this.table = element;
		this.id = this.table.id || false;

		this.source = this.table.dataset.source || false;
		this.info = this.table.hasAttribute("data-info");
		this.paging = this.table.hasAttribute("data-paging");
		this.searching = this.table.hasAttribute("data-searching");
		this.noRowsText = this.table.dataset.noRowsText || "No data found...";
		this.extraData = {};

		this.oldData = null;

		this.init();
	}

	static ScanAndCreate() {
		$("table[role='table']").each((ids, el) => {
			Table.INSTANCES[el.id] = new Table(el);
		});
	}

	init = async () => {
		// Helpers.toggleWait();
		this.createStructure();
		await this.getData();
		this.createHeader();
		this.createRows();
		// Helpers.toggleWait();
	};

	createStructure = () => {
		if (!this.table.classList.contains('table')) this.table.classList.add("table");
		if (!this.table.classList.contains('card-table')) this.table.classList.add("card-table");
		if (!this.table.classList.contains('table-vcenter')) this.table.classList.add("table-vcenter");
		if (!this.table.classList.contains('text-nowrap')) this.table.classList.add("text-nowrap");
		if (!this.table.classList.contains('datatable')) this.table.classList.add("datatable");

		this.thead = $(this.table).find("thead")[0];
		this.tbody = $(this.table).find("tbody")[0];

		if (undefined === this.thead) {
			this.thead = document.createElement("thead");
			this.table.appendChild(this.thead);
		}

		if (undefined === this.tbody) {
			this.tbody = document.createElement("tbody");
			this.table.appendChild(this.tbody);
		}
	};

	createHeader = () => {
		if (this.oldData !== null && JSON.stringify(this.oldData?.columns) === JSON.stringify(this.data.columns)) return;
		this.clearHeader();

		let tr = document.createElement("tr");

		$(this.data.columns).each((i, column) => {
			let th = document.createElement("th");

			if (column.class) th.classList.add(column.class);
			if (column.width) th.style.width = Number.isInteger(column.width) ? column.width + "px" : column.width;
			if (column.title) th.innerHTML = column.title;
			if (column.type === "checkbox") {
				this.checkboxAll = document.createElement("input");
				this.checkboxAll.type = "checkbox";
				this.checkboxAll.classList.add("form-check-input", "m-0", "align-middle");
				this.checkboxAll.onchange = (e) => {
					if (this.checkboxAll.checked) this.checkAll();
					else this.uncheckAll();
				};

				th.appendChild(this.checkboxAll);
			}

			tr.appendChild(th);
		});

		this.thead.appendChild(tr);
	};

	createRows = () => {
		if (this.oldData !== null && JSON.stringify(this.oldData?.rows) === JSON.stringify(this.data.rows)) return;
		this.clearRows();

		if (this.data.hasOwnProperty('rows') === false || this.data.rows.length === 0) {
			let tr = document.createElement("tr");
			let td = document.createElement("td");
			td.setAttribute("colspan", this.data.columns.length);
			td.innerHTML = this.data.noRowsText || this.noRowsText;

			tr.appendChild(td);
			this.tbody.appendChild(tr);

		} else {
			$(this.data.rows).each((i, row) => {
				let tr = document.createElement("tr");
				if (this.data?.format?.row?.backgroundColorValue) tr.style.backgroundColor = Helpers.getObjectValue(row, this.data?.format?.row?.backgroundColorValue);
				if (this.data?.format?.row?.textColorValue) tr.style.color = Helpers.getObjectValue(row, this.data?.format?.row?.textColorValue);

				$(this.data.columns).each((j, column) => {
					let td = document.createElement("td");

					if (column.type === "checkbox") {
						let checkbox = document.createElement("input");
						checkbox.type = "checkbox";
						checkbox.classList.add("form-check-input", "m-0", "align-middle");
						checkbox.value = Helpers.getObjectValue(row, column.data);
						checkbox.onchange = () => {
							this.checkboxCountCheck();
						};

						td.appendChild(checkbox);
					} else if (column.type === "icon") {
						let iconWrapper = document.createElement("span");
						let icon = Helpers.formatValue(Helpers.getObjectValue(row, column.data)[0], column?.type || 'string', column?.format, row);

						if (column.hoverValue && Helpers.getObjectValue(row, column.hoverValue)[0]) iconWrapper.title = Helpers.getObjectValue(row, column.hoverValue)[0];

						iconWrapper.innerHTML = icon;
						td.appendChild(iconWrapper);
					} else {
						td.innerHTML = Helpers.formatValue(Helpers.getObjectValue(row, column.data)[0], column?.type || 'string', column?.format, row);
					}

					tr.appendChild(td);
				});

				this.tbody.appendChild(tr);
			});
		}
	};

	getData = () => {
		if (!this.source) return;

		return $.get(this.source, this.extraData).done(data => {
			this.data = data;
		});
	};

	clear = () => {
		this.clearHeader();
		this.clearRows();
	};

	clearHeader = () => {
		this.thead.innerHTML = "";
	};

	clearRows = () => {
		this.tbody.innerHTML = "";
	};

	reload = async () => {
		this.oldData = this.data;
		await this.getData();
		this.createHeader();
		this.createRows();
	};

	checkAll = () => {
		$(this.tbody).find("[type='checkbox']").prop("checked", true);
	};

	uncheckAll = () => {
		$(this.tbody).find("[type='checkbox']").prop("checked", false);
	};

	checkboxCountCheck = () => {
		let totalRows = $(this.tbody).find("[type='checkbox']").length;
		let checkedRows = $(this.tbody).find("[type='checkbox']:checked").length;

		if (totalRows === checkedRows) this.checkboxAll.checked = true;
		else this.checkboxAll.checked = false;
	};

	getSelectedValue = () => {
		let values = [];

		$.each($(this.tbody).find("[type='checkbox']:checked"), (i, cb) => {
			values.push(cb.value);
		});

		return values.join("-");
	};

	addExtraData = (key, value) => {
		this.extraData[key] = value;
	};
};