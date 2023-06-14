import Helpers from "./Helpers.js";

export default class Table {
	static INSTANCES = {};

	constructor(element) {
		this.element = element;
		this.id = this.element.id || false;

		this.source = this.element.dataset.source || false;
		this.noRowsText = this.element.dataset.noRowsText || "No Data Found...";
		this.doubleClick = this.element.dataset.doubleClick || false;
		this.autoRefresh = this.element.dataset.autoRefresh || false;
		this.small = this.element.hasAttribute("data-small");
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
		this.createStructure();
		await this.getData();
		this.createHeader();
		this.createRows();
		if (this.autoRefresh) this.startAutoRefresh();
	};

	createStructure = () => {
		if (!this.element.classList.contains('table')) this.element.classList.add("table");
		if (!this.element.classList.contains('table-responsive')) this.element.classList.add("table-responsive");
		if (!this.element.classList.contains('card-table')) this.element.classList.add("card-table");
		if (!this.element.classList.contains('table-vcenter')) this.element.classList.add("table-vcenter");
		if (!this.element.classList.contains('table-mobile-md')) this.element.classList.add("table-mobile-md");
		if (!this.element.classList.contains('datatable')) this.element.classList.add("datatable");
		if (this.small && !this.element.classList.contains("table-sm")) this.element.classList.add("table-sm");

		this.thead = $(this.element).find("thead")[0];
		this.tbody = $(this.element).find("tbody")[0];

		if (undefined === this.thead) {
			this.thead = document.createElement("thead");
			this.element.appendChild(this.thead);
		}

		if (undefined === this.tbody) {
			this.tbody = document.createElement("tbody");
			this.element.appendChild(this.tbody);
		}
	};

	createHeader = () => {
		if (this.oldData !== null && JSON.stringify(this.oldData?.columns) === JSON.stringify(this.data.columns)) return;
		this.clearHeader();

		let tr = document.createElement("tr");

		$(this.data.columns).each((i, column) => {
			let th = document.createElement("th");

			if (column.class) th.classList.add(...column.class);
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
				if (row.id) tr.setAttribute("data-id", row.id);

				if (this.data?.format?.row?.backgroundColorValue) tr.style.backgroundColor = Helpers.getObjectValue(row, this.data?.format?.row?.backgroundColorValue) || "";
				if (this.data?.format?.row?.textColorValue) tr.style.color = Helpers.getObjectValue(row, this.data?.format?.row?.textColorValue) || "";

				$(this.data.columns).each((j, column) => {
					let td = document.createElement("td");
					if (column.class) td.classList.add(...column.class);
					if (column.title) td.dataset.label = column.title;

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
						let icon = Helpers.formatValue(Helpers.getObjectValue(row, column.data)[0] || "", column?.type || 'string', column?.format, row);

						if (column.hoverValue && Helpers.getObjectValue(row, column.hoverValue)[0] || "") iconWrapper.title = Helpers.getObjectValue(row, column.hoverValue)[0];

						iconWrapper.innerHTML = icon;
						td.appendChild(iconWrapper);
					} else if (column.type === "badge") {
						let badge = document.createElement("span");
						badge.classList.add("badge");

						if (column.backgroundColor) badge.classList.add(`bg-${Helpers.getObjectValue(row, column.backgroundColor)[0] || ""}`);
						if (column.backgroundColorCustom) badge.style.backgroundColor = Helpers.getObjectValue(row, column.backgroundColorCustom)[0] || "";
						if (column.data) badge.innerHTML = Helpers.getObjectValue(row, column.data)[0] || "";

						td.appendChild(badge);
					} else if (column.type === "url") {
						let url = document.createElement("A");
						url.href = `http://${Helpers.getObjectValue(row, column.data)[0]}`;
						url.target = "_blank";
						url.innerHTML = Helpers.getObjectValue(row, column.data)[0];

						td.appendChild(url);
					} else {
						td.innerHTML = Helpers.formatValue(Helpers.getObjectValue(row, column.data)[0] || "", column?.type || 'string', column?.format, row);
					}

					tr.appendChild(td);
				});

				if (this.doubleClick) tr.ondblclick = () => {
					this.uncheckAll();
					$(tr).find("input:checkbox").prop("checked", true);
					window[this.doubleClick]();
				};

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

	removeExtraData = (key) => {
		delete this.extraData[key];
	};

	clearExtraData = () => {
		this.extraData = {};
	};

	startAutoRefresh = () => {
		setInterval(() => {
			this.reload();
		}, this.autoRefresh * 1000);
	};
};