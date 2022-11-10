class Select {
    static INSTANCES = {};

    constructor(element) {
        this.select = element;
        this.id = this.select.id || false;

        this.render = {};
        this.render.item = this.select.dataset.renderItem || false;
        this.render.option = this.select.dataset.renderOption || false;
        this.onChange = this.select.dataset.onChange || false;
        this.loadSource = this.select.dataset.loadSource || false;
        this.loadValue = this.select.dataset.loadValue || 'id';
        this.loadLabel = this.select.dataset.loadLabel || false;
        this.defaultValue = this.select.dataset.defaultValue || false;

        this.init();
    }

    init = async () => {
        this.select.setAttribute("role", "select");

        this.createSelect();
        this.disable();
        await this.loadSelect();
        this.setDefaultValue();
        this.enable();
    };

    createSelect = () => {
        let settings = {
            hideSelected: false,
            maxOptions: null,
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            create: false,
            render: {},
            searchField: ['text']
        };

        if (this.render.item) settings.render.item = (data, escape) => { return window[this.render.item](data, escape); };
        if (this.render.option) settings.render.option = (data, escape) => { return window[this.render.option](data, escape); };
        if (this.onChange) settings.onChange = (value) => { window[this.onChange](value); };

        if (this.loadSource && this.loadValue && this.loadLabel) {
            settings.valueField = this.loadValue;
            settings.labelField = this.loadLabel;
            settings.searchField = [this.loadLabel];

            settings.load = (query, callback) => {
                $.get(this.loadSource).done(data => {
                    data = JSON.parse(data);
                    callback(data.items);
                }).fail(() => {
                    callback();
                });
            };
        }

        this.tomSelect = new TomSelect(this.select, settings);
    };

    loadSelect = () => {
        if (!this.loadSource) return;

        return new Promise((resolve, reject) => {
            this.tomSelect.on("load", (data) => {
                resolve();
            });

            this.tomSelect.load();
        });


    };

    setDefaultValue = () => {
        if (this.defaultValue) {
            this.setValue(this.defaultValue);
        }
    };

    setValue = (value) => {
        this.tomSelect.setValue(value, true);
    };

    enable = () => {
        if (this.tomSelect != undefined) this.tomSelect.enable();
        else this.select.disabled = false;
    };

    disable = () => {
        if (this.tomSelect != undefined) this.tomSelect.disable();
        else this.select.disabled = true;
    };
}