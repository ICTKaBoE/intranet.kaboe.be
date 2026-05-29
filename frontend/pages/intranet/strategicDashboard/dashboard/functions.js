import Chart from "../../../../shared/default/js/object/Chart.js";
import Helpers from "../../../../shared/default/js/object/Helpers.js";
import List from "../../../../shared/default/js/object/List.js";
import Rating from "../../../../shared/default/js/object/Rating.js";

window.toggleBody = (id) => {
  document.getElementById(`cb-${id}`).classList.toggle("d-none");
  if (document.getElementById(`cb-${id}`).classList.contains("d-none")) {
    document.getElementById(`icon-${id}`).classList.remove("ti-chevron-up");
    document.getElementById(`icon-${id}`).classList.add("ti-chevron-down");
  } else {
    document.getElementById(`icon-${id}`).classList.remove("ti-chevron-down");
    document.getElementById(`icon-${id}`).classList.add("ti-chevron-up");
  }
};

window.loadNext = () => {
  [Rating, Chart].forEach((c) => c.ScanAndCreate());
};
