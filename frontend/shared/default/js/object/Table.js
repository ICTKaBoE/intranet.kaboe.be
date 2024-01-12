import Button from "./Button.js";
import Helpers from "./Helpers.js";

export default class Table {
    static INSTANCES = {};

    constructor(element) {
        this.element = element;
        this.id = this.element.id || false;

        this.source = this.element.dataset.source || false;
        this.noRowsText = this.element.dataset.noRowsText || "No Data Found...";
        this.noCheckbox = this.element.hasAttribute("data-no-checkbox");
        this.doubleClick = this.element.dataset.doubleClick || false;
        this.autoRefresh = this.element.dataset.autoRefresh || false;
        this.small = this.element.hasAttribute("data-small");

        this.paging = this.element.hasAttribute("data-paging");
        this.currentPage = 0;
        this.pageSize = this.element.dataset.pageSize || 25;
        this.pagePosition = this.element.dataset.pagePosition || "top-bottom";

        this.extraData = {};
        this.buttons = {};

        this.usedIcons = {};

        this.oldData = null;

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

    init = async () => {
        this.createStructure();
        await this.getData();
        this.createHeader();
        this.createRows();

        if (this.paging) {
            this.createPaging();
            this.fillPaging();
            this.cutRows();
            this.cutPagingButtons();
            this.setActivePagingButton();
        }

        if (this.autoRefresh) this.startAutoRefresh();
        this.checkButtonStates();
    };

    reload = async () => {
        this.oldData = this.data;
        await this.getData();
        this.uncheckAll();
        this.createHeader();
        this.createRows();

        if (this.paging) {
            this.clearPaging();
            this.createPaging();
            this.fillPaging();
            this.setPage(this.currentPage);
        }

        this.uncheckAll();
        this.checkboxCountCheck();
    };

    createStructure = () => {
        if (!this.element.classList.contains("table"))
            this.element.classList.add("table");
        if (!this.element.classList.contains("table-responsive"))
            this.element.classList.add("table-responsive");
        if (!this.element.classList.contains("card-table"))
            this.element.classList.add("card-table");
        if (!this.element.classList.contains("table-vcenter"))
            this.element.classList.add("table-vcenter");
        if (!this.element.classList.contains("table-mobile-md"))
            this.element.classList.add("table-mobile-md");
        if (!this.element.classList.contains("datatable"))
            this.element.classList.add("datatable");
        if (this.small && !this.element.classList.contains("table-sm"))
            this.element.classList.add("table-sm");

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

    createPaging = () => {
        if (this.pagePosition.includes("top")) {
            if (undefined == this.pagingContainerTop) {
                this.pagingContainerTop = document.createElement("div");
                this.pagingContainerTop.classList.add(
                    "btn-list",
                    "p-2",
                    "ms-auto"
                );
                $(this.pagingContainerTop).insertBefore(this.element);
            }

            this.btnPreviousPageTop = new Button(null, {
                type: "icon",
                icon: "chevron-left",
                title: "Vorige pagina",
                onclick: () => {
                    this.previousPage();
                },
            }).write();

            this.btnNextPageTop = new Button(null, {
                type: "icon",
                icon: "chevron-right",
                title: "Volgende pagina",
                onclick: () => {
                    this.nextPage();
                },
            }).write();

            this.pagingContainerTop.appendChild(this.btnPreviousPageTop);
            this.pagingContainerTop.appendChild(this.btnNextPageTop);
        }

        if (this.pagePosition.includes("bottom")) {
            if (undefined == this.pagingContainerBottom) {
                this.pagingContainerBottom = document.createElement("div");
                this.pagingContainerBottom.classList.add(
                    "btn-list",
                    "m-2",
                    "ms-auto"
                );
                $(this.pagingContainerBottom).insertAfter(this.element);
            }

            this.btnPreviousPageBottom = new Button(null, {
                type: "icon",
                icon: "chevron-left",
                title: "Vorige pagina",
                onclick: () => {
                    this.previousPage();
                },
            }).write();

            this.btnNextPageBottom = new Button(null, {
                type: "icon",
                icon: "chevron-right",
                title: "Volgende pagina",
                onclick: () => {
                    this.nextPage();
                },
            }).write();

            this.pagingContainerBottom.appendChild(this.btnPreviousPageBottom);
            this.pagingContainerBottom.appendChild(this.btnNextPageBottom);
        }
    };

    clearPaging = () => {
        if (this.pagePosition.includes("top"))
            this.pagingContainerTop.innerHTML = "";
        if (this.pagePosition.includes("bottom"))
            this.pagingContainerBottom.innerHTML = "";
    };

    fillPaging = () => {
        this.pageAmount = (this.data.rows?.length ? Math.ceil(this.data.rows?.length / this.pageSize) : 0);
        if (this.pageAmount == 0) this.pageAmount = 1;

        if (this.pagePosition.includes("top")) {
            for (let i = 0; i < this.pageAmount; i++) {
                let btnTop = new Button(null, {
                    text: i + 1,
                    title: `Ga naar pagina ${i + 1}`,
                    onclick: () => {
                        this.setPage(i);
                    },
                }).write();

                btnTop.setAttribute("data-index", i);
                $(btnTop).insertBefore(this.btnNextPageTop);
            }
        }

        if (this.pagePosition.includes("bottom")) {
            for (let i = 0; i < this.pageAmount; i++) {
                let btnBottom = new Button(null, {
                    text: i + 1,
                    title: `Ga naar pagina ${i + 1}`,
                    onclick: () => {
                        this.setPage(i);
                    },
                }).write();

                btnBottom.setAttribute("data-index", i);
                $(btnBottom).insertBefore(this.btnNextPageBottom);
            }
        }
    };

    previousPage = () => {
        if (this.currentPage == 0) return;
        this.setPage((this.currentPage -= 1));
    };

    nextPage = () => {
        if (this.currentPage == this.pageAmount - 1) return;
        this.setPage((this.currentPage += 1));
    };

    setPage = (page) => {
        this.currentPage = page;
        this.cutRows();
        this.cutPagingButtons();
        this.setActivePagingButton();
    };

    cutPagingButtons = () => {
        if (this.pagePosition.includes("top")) {
            $(this.pagingContainerTop)
                .find("button[data-index]")
                .each((i, el) => {
                    if (
                        parseInt(el.dataset.index) >= this.currentPage - 2 &&
                        parseInt(el.dataset.index) <= this.currentPage + 2
                    )
                        new Button(el).show();
                    else new Button(el).hide();
                });
        }

        if (this.pagePosition.includes("bottom")) {
            $(this.pagingContainerBottom)
                .find("button[data-index]")
                .each((i, el) => {
                    if (
                        parseInt(el.dataset.index) >= this.currentPage - 2 &&
                        parseInt(el.dataset.index) <= this.currentPage + 2
                    )
                        new Button(el).show();
                    else new Button(el).hide();
                });
        }
    };

    setActivePagingButton = () => {
        if (this.pagePosition.includes("top")) {
            $(this.pagingContainerTop)
                .find("button[data-index]")
                .each((i, el) => {
                    if (parseInt(el.dataset.index) == this.currentPage)
                        el.classList.add("btn-primary");
                    else el.classList.remove("btn-primary");
                });
        }

        if (this.pagePosition.includes("bottom")) {
            $(this.pagingContainerBottom)
                .find("button[data-index]")
                .each((i, el) => {
                    if (parseInt(el.dataset.index) == this.currentPage)
                        el.classList.add("btn-primary");
                    else el.classList.remove("btn-primary");
                });
        }
    };

    createHeader = () => {
        if (
            this.oldData !== null &&
            JSON.stringify(this.oldData?.columns) ===
            JSON.stringify(this.data.columns)
        )
            return;
        this.clearHeader();

        let tr = document.createElement("tr");

        $(this.data.columns).each((i, column) => {
            let th = document.createElement("th");

            if (column.class) th.classList.add(...column.class);
            if (column.width)
                th.style.width = Number.isInteger(column.width)
                    ? column.width + "px"
                    : column.width;
            if (column.title) th.innerHTML = column.title;
            if (column.titleIcon)
                th.innerHTML = `<i class="icon ti ti-${column.titleIcon}" title="${column.title}"></i>`;
            if (column.type === "checkbox") {
                if (this.noCheckbox) return;
                this.checkboxAll = document.createElement("input");
                this.checkboxAll.type = "checkbox";
                this.checkboxAll.classList.add(
                    "form-check-input",
                    "m-0",
                    "align-middle"
                );
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
        if (
            this.oldData !== null &&
            JSON.stringify(this.oldData?.rows) ===
            JSON.stringify(this.data.rows)
        )
            return;
        this.clearRows();

        if (
            this.data.hasOwnProperty("rows") === false ||
            this.data.rows.length === 0
        ) {
            let tr = document.createElement("tr");
            let td = document.createElement("td");
            td.setAttribute("colspan", this.data.columns.length);
            td.innerHTML = this.data.noRowsText || this.noRowsText;

            tr.appendChild(td);
            this.tbody.appendChild(tr);
        } else {
            this.data.rows = Helpers.cleanRowIndexes(this.data.rows);

            $(this.data.rows).each((i, row) => {
                let tr = document.createElement("tr");
                tr.dataset.index = i;
                if (row.id) tr.setAttribute("data-id", row.id);
                if (this.paging) tr.classList.add("d-none");

                if (this.data?.format?.row?.backgroundColorValue)
                    tr.style.backgroundColor =
                        Helpers.getObjectValue(
                            row,
                            this.data?.format?.row?.backgroundColorValue
                        ) || "";
                if (this.data?.format?.row?.textColorValue)
                    tr.style.color =
                        Helpers.getObjectValue(
                            row,
                            this.data?.format?.row?.textColorValue
                        ) || "";

                $(this.data.columns).each((j, column) => {
                    let td = document.createElement("td");
                    if (column.class) td.classList.add(...column.class);
                    if (column.title) td.dataset.label = column.title;

                    if (column.type === "checkbox") {
                        if (this.noCheckbox) return;
                        let checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.classList.add(
                            "form-check-input",
                            "m-0",
                            "align-middle"
                        );
                        checkbox.value = Helpers.getObjectValue(
                            row,
                            column.data
                        );
                        checkbox.onchange = () => {
                            this.checkboxCountCheck();
                        };

                        td.appendChild(checkbox);
                    } else if (column.type === "icon") {
                        let icon = document.createElement("i");
                        icon.classList.add("icon", "ti");
                        icon.classList.add(
                            "ti-" +
                            Helpers.formatValue(
                                Helpers.getObjectValue(
                                    row,
                                    column.data
                                )[0] || "",
                                column?.type || "string",
                                column?.format,
                                row
                            )
                        );

                        if (
                            (column.hoverValue &&
                                Helpers.getObjectValue(
                                    row,
                                    column.hoverValue
                                )[0]) ||
                            ""
                        )
                            iconWrapper.title = Helpers.getObjectValue(
                                row,
                                column.hoverValue
                            )[0];

                        td.appendChild(icon);
                    } else if (column.type === "badge") {
                        let badge = document.createElement("span");
                        badge.classList.add("badge", "text-white");

                        if (column.backgroundColor)
                            badge.classList.add(
                                `bg-${Helpers.getObjectValue(
                                    row,
                                    column.backgroundColor
                                )[0] || ""
                                }`
                            );
                        if (column.backgroundColorCustom)
                            badge.style.backgroundColor =
                                Helpers.getObjectValue(
                                    row,
                                    column.backgroundColorCustom
                                )[0] || "";
                        if (column.data)
                            badge.innerHTML =
                                Helpers.getObjectValue(row, column.data)[0] ||
                                "";

                        td.appendChild(badge);
                    } else if (column.type === "url") {
                        let url = document.createElement("A");
                        url.href = `http://${Helpers.getObjectValue(row, column.data)[0]
                            }`;
                        url.target = "_blank";
                        url.innerHTML = Helpers.getObjectValue(
                            row,
                            column.data
                        )[0];

                        td.appendChild(url);
                    } else if (column.type === "password") {
                        if (
                            Helpers.getObjectValue(row, column.data)[0] == "" ||
                            Helpers.getObjectValue(row, column.data)[0] == null
                        ) {
                        } else {
                            let group = document.createElement("div");
                            group.classList.add("row", "m-0");

                            let passwordLabel = document.createElement("span");
                            passwordLabel.classList.add("row", "d-none");
                            passwordLabel.innerHTML = Helpers.getObjectValue(
                                row,
                                column.data
                            )[0];
                            group.appendChild(passwordLabel);

                            let passwordHashLabel =
                                document.createElement("span");
                            passwordHashLabel.classList.add("row");
                            passwordHashLabel.innerHTML = Helpers.formatValue(
                                Helpers.getObjectValue(row, column.data)[0] ||
                                "",
                                column?.type || "string",
                                column?.format,
                                row
                            );
                            group.appendChild(passwordHashLabel);

                            let eyeContainer = document.createElement("span");
                            eyeContainer.classList.add("col-1");

                            let eyeIcon = document.createElement("i");
                            eyeIcon.classList.add("icon", "ti", "ti-eye");

                            eyeContainer.appendChild(eyeIcon);

                            eyeContainer.onmousedown = () => {
                                passwordHashLabel.classList.add("d-none");
                                passwordLabel.classList.remove("d-none");
                            };

                            eyeContainer.onmouseup = () => {
                                passwordLabel.classList.add("d-none");
                                passwordHashLabel.classList.remove("d-none");
                            };

                            group.appendChild(eyeContainer);

                            td.appendChild(group);
                        }
                    } else {
                        td.innerHTML = Helpers.formatValue(
                            Helpers.getObjectValue(row, column.data)[0] || "",
                            column?.type || "string",
                            column?.format,
                            row
                        );
                    }

                    tr.appendChild(td);
                });

                if (this.doubleClick)
                    tr.ondblclick = () => {
                        this.uncheckAll();
                        $(tr).find("input:checkbox").prop("checked", true);
                        this.checkboxCountCheck();
                        window[this.doubleClick]();
                    };

                this.tbody.appendChild(tr);
            });
        }
    };

    cutRows = () => {
        if (!this.data.rows?.length) return;
        let startIndex = this.currentPage * parseInt(this.pageSize);
        let endIndex =
            this.currentPage * parseInt(this.pageSize) +
            parseInt(this.pageSize);

        $(this.tbody.rows).each((i, row) => {
            if (
                parseInt(row.dataset.index) >= startIndex &&
                parseInt(row.dataset.index) <= endIndex
            )
                row.classList.remove("d-none");
            else row.classList.add("d-none");
        });
    };

    getData = () => {
        if (!this.source) return;

        return $.get(this.source, this.extraData).done((data) => {
            this.data = data;
        });
    };

    disable = () => {
        $(this.element).find("[type='checkbox']").prop("disabled", true);
    };

    enable = () => {
        $(this.element).find("[type='checkbox']").prop("disabled", false);
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

    checkAll = () => {
        if (this.noCheckbox) return;
        $(this.tbody).find("[type='checkbox']").prop("checked", true);
        this.checkButtonStates();
    };

    uncheckAll = () => {
        if (this.noCheckbox) return;
        $(this.tbody).find("[type='checkbox']").prop("checked", false);
        this.checkButtonStates();
    };

    checkboxCountCheck = () => {
        if (this.noCheckbox || undefined == this.checkboxAll) return;
        let totalRows = $(this.tbody).find("[type='checkbox']").length;
        let checkedRows = $(this.tbody).find(
            "[type='checkbox']:checked"
        ).length;

        if (totalRows === checkedRows) this.checkboxAll.checked = true;
        else this.checkboxAll.checked = false;

        this.checkButtonStates();
    };

    getSelectedValue = () => {
        if (this.noCheckbox) return;
        let values = [];

        $.each($(this.tbody).find("[type='checkbox']:checked"), (i, cb) => {
            values.push(cb.value);
        });

        return values.join("-").trim();
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

    attachButton = (button, showIf = null) => {
        if (!button instanceof Button) return;

        this.buttons[button.id] = {
            button: button,
            showIf: showIf,
        };
    };

    checkButtonStates = () => {
        if (this.noCheckbox) return;
        for (let btn in this.buttons) {
            if (Object.hasOwnProperty.call(this.buttons, btn)) {
                let button = this.buttons[btn];

                if (
                    eval(
                        this.getSelectedValue()
                            .split("-")
                            .filter((n) => n).length + button.showIf
                    )
                )
                    button.button.show();
                else button.button.hide();
            }
        }
    };
}
