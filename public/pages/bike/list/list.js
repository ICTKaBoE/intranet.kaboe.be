upnOnChange = (value) => {
    Table.INSTANCES[tableId].reload({ upn: value });
};