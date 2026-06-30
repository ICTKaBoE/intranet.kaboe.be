import MasterObject from "../MasterObject.js";
import Helpers from "./Helpers.js";

export default class List extends MasterObject {
  static OBJ_SELECTOR = "[role='list']";
  static OBJ_ID_PREFIX = "lst";

  constructor(element) {
    super();

    this.element = element;
    this.id = this.element.id || false;

    this.source = this.element.dataset.source || false;
    this.extraDataString = this.element.dataset.extra || false;
    this.template = this.element.dataset.template || false;
    this.limit = this.element.dataset.limit || 200;
    this.stopCheckNext = false;
    this.noItemsText = this.element.dataset.noItemsText || false;
    this.afterLoadCallback = this.element.dataset.afterLoadCallback || false;

    this.element.removeAttribute("data-template");

    this.extraData = {};

    if (this.extraDataString) {
      let extraData = this.extraDataString
        .replace("[", "")
        .replace("]", "")
        .split("|");

      extraData.forEach((v) => {
        v = v.split("=");
        this.extraData[v[0]] = v[1];
      });
    }

    this.extraData.template = this.template;
    this.extraData.limit = this.limit;
    this.extraData.page = 0;

    this.loaded = false;
    this.init();
  }

  init = async () => {
    this.filter();
    await this.getData();
    this.fill();
    if (!this.stopCheckNext) await this.checkNext();
    this.loaded = true;

    if (this.afterLoadCallback) window[this.afterLoadCallback]();
  };

  reload = async () => {
    Helpers.toggleWait();
    this.loaded = false;
    this.stopCheckNext = true;
    this.extraData.page = 0;
    this.filter();
    await this.getData();
    this.fill();
    if (!this.stopCheckNext) await this.checkNext();
    this.loaded = true;

    if (this.afterLoadCallback) window[this.afterLoadCallback]();
    Helpers.toggleWait();
  };

  getData = () => {
    if (!this.source) return;

    return $.get(this.source, this.extraData).done((data) => {
      this.data = data;
    });
  };

  fill = () => {
    let data = this.data.raw;
    if (this.data?._raw == "base64") data = atob(this.data.raw);

    if (this.extraData?.page == 0) this.element.innerHTML = data;
    else this.element.innerHTML += data;
  };

  search = async (value) => {
    this.stopCheckNext = value.length > 0;
    this.extraData.page = 0;
    this.extraData.search = value;

    await this.getData();
    this.fill();
    this.checkNext();
  };

  setExtraLoadParam = (key, value) => {
    this.extraData[key] = value;
  };

  checkNext = () => {
    if (this.data.next) {
      setTimeout(async () => {
        this.extraData.page++;

        await this.getData();
        this.fill();
        if (!this.stopCheckNext) this.checkNext();
      }, 1000);
    }
  };

  filter = () => {
    let modal = document.getElementById("modal-filter");
    let filter = JSON.parse(
      window.localStorage.getItem(
        `filter_${modal.dataset.module}_${modal.dataset.page}`,
      ),
    );

    if (filter)
      for (const [k, v] of Object.entries(filter)) this.setExtraLoadParam(k, v);
  };
}
