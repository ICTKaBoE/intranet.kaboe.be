item = (data, escape) => {
    if (data.icon) {
        return `<div><span class="dropdown-item-indicator">${data.icon}</span>${escape(data.text)}</div>`;
    }

    return `<div>${escape(data.text)}</div>`;
};

option = (data, escape) => {
    if (data.icon) {
        return `<div><span class="dropdown-item-indicator">${data.icon}</span>${escape(data.text)}</div>`;
    }

    return `<div>${escape(data.text)}</div>`;
};

toolOnChange = (value) => {
    Table.INSTANCES[rightsTableId].reload({ id: value });
};