import Checkbox from "../../../../../shared/default/js/object/Checkbox.js";
import Select from "../../../../../shared/default/js/object/Select.js";
import Helpers from "../../../../../shared/default/js/object/Helpers.js";
import Form from "../../../../../shared/default/js/object/Form.js";

window.renderOptgroupItem = (data, escape) => {
  return `<div>${data?.formatted?.name || data.name}</div>`;
};

window.locationView = (info) => {
  let location = Select.GetInstance("location").getItemDetails();
  if (!location) return;

  if (location?.linked?.category?.extendedOptions || location.extendedOptions)
    document.getElementById("location-O").classList.remove("d-none");
  else document.getElementById("location-O").classList.add("d-none");
};

window.partyView = (info) => {
  let party = Select.GetInstance("party").getItemDetails();
  if (!party) return;

  $(`[id='party-E']`).addClass("d-none");
  $(`[id='party-O']`).addClass("d-none");
  $(`[id='party-I']`).addClass("d-none");
  $(`[id='party-${party.extendedOptions}']`).removeClass("d-none");
};

window.policeView = () => {
  let police = Checkbox.GetInstance("chbPolice").getValue();

  if (police) document.getElementById("police-Y").classList.remove("d-none");
  else document.getElementById("police-Y").classList.add("d-none");
};

window.supervisionView = () => {
  let supervision = Checkbox.GetInstance("chbSupervision").getValue();

  if (supervision)
    document.getElementById("supervision-Y").classList.remove("d-none");
  else document.getElementById("supervision-Y").classList.add("d-none");
};

window.witnessView = () => {
  let witness = Checkbox.GetInstance("chbWitness").getValue();

  if (witness) {
    document.getElementById("witness-Y").classList.remove("d-none");
    document.getElementById("witness-N").classList.add("d-none");
  } else {
    document.getElementById("witness-Y").classList.add("d-none");
    document.getElementById("witness-N").classList.remove("d-none");
  }
};

window.witnessAfterView = () => {
  let witnessAfter = Checkbox.GetInstance("chbWitnessAfter").getValue();

  if (witnessAfter) {
    document.getElementById("witnessAfter-Y").classList.remove("d-none");
    document.getElementById("whenAndWho-N").classList.add("d-none");
  } else {
    document.getElementById("witnessAfter-Y").classList.add("d-none");
    document.getElementById("whenAndWho-N").classList.remove("d-none");
  }
};

Helpers.CheckAllLoaded(() => {
  setTimeout(() => {
    window.locationView();
    window.partyView();
    window.policeView();
    window.supervisionView();
    window.witnessView();
    window.witnessAfterView();
  }, 250);
}, [Form, Select]);
