addSchools = () => {
    toggleModal(modalAddId);
};

editSchools = () => {
    let ids = Table.INSTANCES[tableId].getCheckedValues();
    if (ids.length > 1) toggleModal('pageError', 'Er kan maar 1 school per keer bewerkt worden!', 10);
};

deleteSchools = () => {

};