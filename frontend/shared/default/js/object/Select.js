import MasterObject from "../MasterObject.js";
import Helpers from "./Helpers.js";

export default class Select extends MasterObject {
  static OBJ_SELECTOR = "select";

  constructor(element) {
    super();

    this.element = element;
    this.id = this.element.id || false;

    this.noCreate = this.element.hasAttribute("data-no-create");
    this.render = {};
    this.render.item = this.element.dataset.renderItem || false;
    this.render.option = this.element.dataset.renderOption || false;
    this.onChange = this.element.dataset.onChange || false;
    this.source = this.element.dataset.loadSource || false;
    this.value = this.element.dataset.value || "id";
    this.label = this.element.dataset.label || "name";
    this.defaultDetails = this.element.dataset.defaultDetails || false;
    this.defaultValue = this.element.dataset.defaultValue || false;
    this.defaultExtraData = this.element.dataset.extra || false;
    this.multiple = this.element.hasAttribute("multiple");
    this.search = this.element.hasAttribute("data-search");
    this.parent = this.element.dataset.parentSelect || false;
    this.defaultDisabled = this.element.hasAttribute("disabled") || false;
    this.optgroup = this.element.dataset.optgroup || "optgroup";
    this.optgroupValue = this.element.dataset.optgroupValue || "id";
    this.optgroupLabel = this.element.dataset.optgroupLabel || "name";
    this.limit = this.element.dataset.limit || 200;
    this.defaultNoLoad = this.element.hasAttribute("data-default-no-load");
    this.defaultNoValue = this.element.hasAttribute("data-default-no-value");
    this.hideIfNoOptions = this.element.hasAttribute("data-hide-if-no-options");
    this.disableIfNoOptions = this.element.hasAttribute(
      "data-disable-if-no-options",
    );

    this.eventListeners = [];
    this.selectedDetails = false;
    this.data = {
      items: [],
    };
    this.loadParams = {};
    this.loadParams.limit = this.limit;
    this.loadParams.page = 0;

    this.stopCheckNext = false;

    if (this.source && this.source.startsWith("[")) {
      let loadSource = this.source.replace("[", "").replace("]", "").split(";");

      this.source = [];
      loadSource.forEach((source) => {
        source = source.split("@");
        this.source[source[0]] = source[1];
      });

      if (!this.defaultDetails)
        this.defaultDetails = Object.keys(this.source)[0];
    }

    if (this.value && this.value.startsWith("[")) {
      let loadValue = this.value.replace("[", "").replace("]", "").split(";");

      this.value = [];
      loadValue.forEach((source) => {
        source = source.split("@");
        this.value[source[0]] = source[1];
      });
    }

    if (this.label && this.label.startsWith("[")) {
      let loadLabel = this.label.replace("[", "").replace("]", "").split(";");

      this.label = [];
      loadLabel.forEach((source) => {
        source = source.split("@");
        this.label[source[0]] = source[1];
      });
    }

    if (this.defaultExtraData) {
      this.defaultExtraData = Object.fromEntries(
        this.defaultExtraData
          .replace("[", "")
          .replace("]", "")
          .split("|")
          .map((v) => v.split("=")),
      );

      this.loadParams = { ...this.defaultExtraData };
    }

    this.loaded = false;
    this.init();
  }

  init = async () => {
    if (this.noCreate) return;
    this.element.setAttribute("role", "select");
    this.element.setAttribute("type", "text");
    this.element.removeAttribute("disabled");
    if (!this.element.classList.contains("form-select"))
      this.element.classList.add("form-select");

    if (!this.defaultNoLoad && this.source) {
      this.loadParams.page = 0;
      await this.getData();
      if (!this.stopCheckNext) await this.checkNext();
    }

    this.createSelect();
    this.disable();
    if (this.parent) this.detectParentAndSetFunctions();
    this.setDefaultValue();
    if (this.defaultNoValue) this.clear();
    if (!this.defaultDisabled) this.enable();
    if (this.hideIfNoOptions) this.checkHide();
    if (this.disableIfNoOptions) this.checkDisable();
    this.loaded = true;
  };

  reload = async () => {
    this.loaded = false;
    this.disable();
    this.clear();
    this.destroy();

    if (this.source) {
      this.loadParams.page = 0;
      await this.getData();
      if (!this.stopCheckNext) await this.checkNext();
    }

    this.createSelect();
    this.setEventListeners();
    this.setDefaultValue();
    if (this.defaultNoValue) this.clear();
    if (!this.defaultDisabled) this.enable();
    if (this.hideIfNoOptions) this.checkHide();
    if (this.disableIfNoOptions) this.checkDisable();
    this.loaded = true;
  };

  setDetails = (id) => {
    this.selectedDetails = id;
    this.data.items = [];
    this.reload();
  };

  createSelect = () => {
    let settings = {
      plugins: this.multiple ? ["remove_button", "checkbox_options"] : [],
      hideSelected: false,
      duplicates: true,
      maxOptions: null,
      maxItems: this.multiple ? null : 1,
      delimiter: this.multiple ? ";" : null,
      copyClassesToDropdown: false,
      dropdownClass: "dropdown-menu ts-dropdown",
      optionClass: "dropdown-item",
      persist: false,
      create: false,
      render: {},
      searchField: ["text"],
      copyClassesToDropdown: false,
      controlInput: this.search ? "<input>" : null,
    };

    if (this.render.item)
      settings.render.item = (data, escape) => {
        return window[this.render.item](data, escape);
      };

    if (this.render.option)
      settings.render.option = (data, escape) => {
        return window[this.render.option](data, escape);
      };

    if (this.onChange)
      settings.onChange = (value) => {
        $(document).ready(() => {
          window[this.onChange](value);
        });
      };

    if (this.optgroup) {
      settings.optgroupField = this.optgroup;
      settings.optgroupValueField = this.optgroupValue;
      settings.optgroupLabelField = this.optgroupLabel;
    }

    if (this.source && this.value && this.label) {
      settings.valueField =
        this.value[this.selectedDetails || this.defaultDetails] || this.value;
      settings.labelField =
        this.label[this.selectedDetails || this.defaultDetails] || this.label;
      settings.searchField = [
        this.label[this.selectedDetails || this.defaultDetails] || this.label,
      ];
    }

    if (this.data?.optgroups && this.optgroup)
      settings.optgroups = this.data.optgroups;
    if (this.data?.items && this.data?.items.length)
      settings.options = this.data.items;

    this.tomSelect = new TomSelect(this.element, settings);
  };

  getData = () => {
    if (!this.source) return;

    return $.get(
      this.source[this.selectedDetails || this.defaultDetails] || this.source,
      this.loadParams,
    ).done((data) => {
      let items = data.items;
      delete data.items;
      this.data = Object.assign(this.data, data);

      if (this.data.items.length == 0) this.data.items = items;
      else this.data.items.push(...items);
    });
  };

  checkNext = async () => {
    if (this.data.next) {
      await Helpers.sleep(100);
      this.loadParams.page++;

      await this.getData();
      if (!this.stopCheckNext) await this.checkNext();
    }
  };

  setDefaultValue = () => {
    if (this.defaultValue) this.setValue(this.defaultValue, false);
  };

  checkHide = () => {
    if (this.data.items.length > 0) this.show();
    else this.hide();
  };

  checkDisable = () => {
    if (this.data.items.length > 0) this.enable();
    else this.disable();
  };

  setValue = (value, silent = false) => {
    this.defaultValue = value;
    Helpers.CheckAllLoaded(() => {
      value = String(value)
        .split(";")
        .map((v) => (isNaN(v) ? v : parseFloat(v)));
      this.tomSelect.setValue(value, silent);
    });
  };

  getValue = () => {
    let items = this.tomSelect.getValue();
    if (Array.isArray(items)) items = items.join(";");
    return items;
  };

  getText = () => {
    let details = this.getItemDetails();
    if (!details) return "";
    return details[
      this.label[this.selectedDetails || this.defaultDetails] || this.label
    ];
  };

  getItemDetails = () => {
    let details = [];
    this.getValue()
      .split(";")
      .forEach((value) => {
        details.push(this.tomSelect.options[value]);
      });

    return details.length == 1 ? details[0] : details;
  };

  addOption = (v, t) => {
    this.tomSelect.addOption({ value: v, text: t });
  };

  setEventListeners = () => {
    Object.keys(this.eventListeners).forEach((key) => {
      this.setEventListener(key, this.eventListeners[key]);
    });
  };

  setEventListener = (event, callback) => {
    this.eventListeners[event] = callback;
    this.tomSelect.on(event, callback);
  };

  clear = () => {
    this.data.items = [];
    if (this.tomSelect != undefined) this.tomSelect.clear();
    this.setDefaultValue();
  };

  enable = (forced = false) => {
    if (this.defaultDisabled && !forced) return;

    if (this.tomSelect != undefined) this.tomSelect.enable();
    else this.element.disabled = false;
  };

  disable = () => {
    if (this.tomSelect != undefined) this.tomSelect.disable();
    else this.element.disabled = true;
  };

  hide = () => {
    this.element.parentElement.classList.add("d-none");
  };

  show = () => {
    this.element.parentElement.classList.remove("d-none");
  };

  destroy = () => {
    if (this.tomSelect != undefined) this.tomSelect.destroy();
  };

  setExtraLoadParam = (key, value) => {
    this.loadParams[key] = value;
  };

  removeExtraLoadParam = (key) => {
    delete this.loadParams[key];
  };

  detectParentAndSetFunctions = () => {
    setTimeout(() => {
      Select.GetInstance(this.parent).setEventListener("change", (value) => {
        this.data.items = [];
        this.setExtraLoadParam(
          this.parent,
          value ?? this.defaultExtraData[this.parent],
        );
        this.reload();
      });
    }, 500);
  };
}
