class Table {
    static INSTANCES = {};

    constructor(element) {
        this.table = element;
        this.id = this.table.id || false;
        this.reloadData = this.table.dataset.reloadData || false;
        this.title = this.table.dataset.title || false;
        this.source = this.table.dataset.source || false;
        this.entries = this.table.dataset.entries || false;
        this.search = this.table.dataset.search || false;
        this.info = this.table.dataset.info || false;
        this.pagination = this.table.dataset.pagination || false;
        this.wrap = (this.table.dataset?.wrap || 'true' === 'true');
        this.clickToEdit = (this.table.dataset?.clickToEdit || 'false' === 'true');

        if (this.title == "false") this.title = false;
        if (this.source == "false") this.source = false;
        if (this.entries == "false") this.entries = false;
        if (this.search == "false") this.search = false;
        if (this.info == "false") this.info = false;
        if (this.pagination == "false") this.pagination = false;

        this.setup = [];
        this.headerData = "";
        this.rowsData = "";

        this.init();
    }

    init = () => {
        this.createLayout();
        this.fetchData();

        if (this.reloadData) {
            setInterval(() => {
                this.reload();
            }, this.reloadData * 1000);
        }
    };

    createLayout = () => {
        if (this.wrap) {
            let card = document.createElement("div");
            card.classList.add("card");

            $(this.table).wrap(card);
        }

        if (this.title) {
            let cardHeader = document.createElement("div");
            cardHeader.classList.add("card-header");

            let title = document.createElement("h3");
            title.innerHTML = this.title;
            cardHeader.appendChild(title);

            $(cardHeader).insertBefore(this.table);
        }

        if (this.entries || this.search) {
            let cardBody = document.createElement("div");
            cardBody.classList.add("card-body", "border-bottom", "py-3");

            let dFlex = document.createElement("div");
            dFlex.classList.add("d-flex");

            let textMuted = document.createElement("div");
            textMuted.classList.add("text-muted");

            let inputContainer = document.createElement("div");

            $(cardBody).insertBefore(this.table);
        }

        this.thead = document.createElement("thead");
        this.tbody = document.createElement("tbody");

        this.table.appendChild(this.thead);
        this.table.appendChild(this.tbody);
    };

    reload = (extraData = {}) => {
        return this.fetchData(extraData);
    };

    fetchData = (extraData = {}) => {
        return $.get(this.source, extraData)
            .done(data => {
                data = JSON.parse(data);

                if (data.setup) this.setup = data.setup;
                if (data.header) this.createHeader(data.header);
                if (data.rows) {
                    if (data.rows.length !== 0) this.createRows(data.rows);
                    else this.createEmptyRow();
                }
            })
            .fail(returnData => {
                alert("Er is een fout gebeurd bij het laden van de tabel!");
            });
    };

    createHeader = (data) => {
        if (this.headerData == data) return;

        this.headerData = data;
        this.thead.innerHTML = "";
        let tr = document.createElement("tr");

        $(this.headerData).each((idx, col) => {
            let th = document.createElement("th");

            switch (col.type) {
                case 'check': {
                    th.classList.add("w-1");
                    let check = document.createElement("input");
                    check.type = "checkbox";
                    check.classList.add("form-check-input", "m-0", "align-middle");
                    check.setAttribute("aria-label", "Selecteer alle rijen");

                    check.onchange = () => {
                        if (check.checked) this.checkAll();
                        else this.uncheckAll();
                    };

                    th.append(check);
                } break;

                case 'icon': {
                    th.classList.add("w-1");
                    th.innerHTML = col.text;
                } break;

                default: {
                    th.style.width = col.width + "px";
                    if (col.text) th.innerHTML = col.text;
                } break;
            }

            tr.append(th);
        });

        this.thead.append(tr);
    };

    createRows = (data) => {
        if (this.rowsData == data) return;
        this.rowsData = data;

        this.tbody.innerHTML = "";
        $(this.rowsData).each((idy, row) => {
            let tr = document.createElement("tr");
            if (this.clickToEdit) tr.onclick = () => {
                if (this.setup?.clickToEdit?.title) tr.title = this.setup.clickToEdit.title;
                if (!this.setup?.clickToEdit?.value) return;

                let url = new URL(window.location.href);
                url.searchParams.set("p", "edit");
                url.searchParams.set("id", row[this.setup.clickToEdit.value]);
                window.location.href = url;
            };

            $(this.headerData).each((idx, col) => {
                let td = document.createElement("td");

                switch (col.type) {
                    case 'check': {
                        let check = document.createElement("input");
                        check.type = "checkbox";
                        check.classList.add("form-check-input", "m-0", "align-middle");
                        check.setAttribute("aria-label", "Selecteer alle rijen");
                        check.value = row[col.value];

                        check.onchange = () => {
                            this.checkIfAllChecked();
                        };

                        td.append(check);
                    } break;

                    case 'icon': {
                        td.innerHTML = row[col.icon];
                    } break;

                    default: {
                        if (col.value) td.innerHTML = row[col.value];
                        if (col.align) td.style.textAlign = col.align;
                    } break;
                }

                tr.append(td);
            });

            this.tbody.append(tr);
        });
    };

    createEmptyRow = () => {
        this.rowsData = [];
        this.tbody.innerHTML = "";

        let tr = document.createElement("tr");
        let td = document.createElement('td');
        td.setAttribute("colspan", this.headerData.length);
        td.classList.add("text-center");
        td.innerHTML = "Geen data!";

        tr.append(td);
        this.tbody.append(tr);
    };

    checkAll = () => {
        $(this.tbody).find("input[type='checkbox']").prop('checked', true);
    };

    uncheckAll = () => {
        $(this.tbody).find("input[type='checkbox']").prop('checked', false);
    };

    checkIfAllChecked = () => {
        let checkboxes = $(this.tbody).find("input[type='checkbox']");
        let checkedBoxes = $(this.tbody).find("input[type='checkbox']:checked");

        $(this.thead).find("input[type='checkbox']").prop('checked', checkboxes.length === checkedBoxes.length);
    };

    getCheckedValues = () => {
        let values = [];
        $(this.tbody).find("input[type='checkbox']:checked").each((i, v) => {
            values.push(v.value);
        });

        return values;
    };
}