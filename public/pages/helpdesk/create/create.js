setCorrectFields = (value) => {
    if (value === 0) {
        $('.form-remark[data-for-type]').addClass("d-none");
    } else {
        $(`.form-remark[data-for-type!='${value}']`).addClass("d-none");
        $(`.form-remark[data-for-type='${value}']`).removeClass("d-none");
    }
}