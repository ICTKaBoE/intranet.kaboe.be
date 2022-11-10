$("select").each((ids, el) => {
    Select.INSTANCES[el.id] = new Select(el);
});

$("table").each((ids, el) => {
    Table.INSTANCES[el.id] = new Table(el);
});

$("[role='list']").each((ids, el) => {
    List.INSTANCES[el.id] = new List(el);
});

$("[role='calendar']").each((ids, el) => {
    Calendar.INSTANCES[el.id] = new Calendar(el);
});

$("[role='datepicker']").each((ids, el) => {
    DatePicker.INSTANCES[el.id] = new DatePicker(el);
});

$("[role='chart']").each((ids, el) => {
    Chart.INSTANCES[el.id] = new Chart(el);
});

$("form").each((ids, el) => {
    Form.INSTANCES[el.id] = new Form(el);
});

let modalOpen = false;

toggleModal = (id, content = false, disappear = false) => {
    if (content) $(`#${id}Content`).html(content);

    let modal = new bootstrap.Modal(document.getElementById(id), { keyboard: false });
    document.getElementById(id).addEventListener('shown.bs.modal', (event) => {
        modalOpen = true;
    });

    document.getElementById(id).addEventListener('hidden.bs.modal', (event) => {
        modalOpen = false;
    });

    if (modalOpen) modal.hide();
    else modal.show();

    if (disappear) {
        let time = disappear;
        let clock = document.getElementById(`${id}Timer`);

        let timer = setInterval(() => {
            if (time == 0) {
                clearInterval(timer);
                modal.hide();
            }

            clock.innerHTML = `(${time})`;
            time--;
        }, 1000);
    }
};

$(document).ready(() => {
    $.get("./app/scripts/GET/pageErrors.php").done(data => {
        data = JSON.parse(data);

        if (data.errors) {
            let errors = "<ul>";

            $(data.errors).each((idx, value) => {
                errors += `<li>${value}</li>`;
            });

            errors += "</ul>";

            toggleModal("pageError", errors, 10);
        }
    });
});