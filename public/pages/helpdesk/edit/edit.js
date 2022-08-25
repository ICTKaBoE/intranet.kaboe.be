submitInfoForm = () => {
    Form.INSTANCES[infoFormId].submit().then(() => {
        List.INSTANCES[historyListId].reload();
    });
};

submitReactForm = () => {
    console.log('submitReactForm');
    List.INSTANCES[historyListId].reload();
    List.INSTANCES[messagesListId].reload();
};