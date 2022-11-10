class List {
    static INSTANCES = {};

    constructor(element) {
        this.list = element;
        this.id = this.list.id || false;
        this.source = this.list.dataset.source || false;
        this.template = this.list.dataset.template || false;

        this.init();
    }

    init = () => {
        this.getTemplate();
        this.createList();
        this.fillList();
    };

    getTemplate = () => {
        if (this.template) return;

        this.template = $(this.list).find("[role='template']").html();
        $(this.list).find("[role='template']").remove();
    };

    createList = () => {
        if (!this.list.classList.contains('list-group')) this.list.classList.add('list-group');
        if (!this.list.classList.contains('list-group-flush')) this.list.classList.add('list-group-flush');
    };

    fillList = () => {
        if (!this.source) return;

        $.get(this.source).done(data => {
            data = JSON.parse(data);

            if (data.items) this.createItems(data.items);
        }).fail(returnData => {
            alert("Er is een fout gebeurd bij het laden van de lijst!");
        });
    };

    createItems = (items) => {
        $.each(items, (idx, item) => {
            let html = this.createHtml(item);
            this.list.insertAdjacentHTML("beforeend", html);
        });
    };

    createHtml = (item) => {
        let outer = $(this.template).clone();
        let inner = $(this.template);

        $.each(item, (key, value) => {
            inner.find(`[data-prefill='${key}']`).html(value);
        });

        return outer.html(inner).html();
    };

    reload = () => {
        this.list.innerHTML = "";
        this.fillList();
    };
}