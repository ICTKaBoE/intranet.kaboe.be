setDistance = (info) => {
    $.post(document.getElementById(calendarId).dataset.action, {
        date: info.dateStr,
        distance: info?.draggedEl?.dataset?.distance
    }).done((data) => {
        data = JSON.parse(data);

        if (data.reload) Calendar.INSTANCES[calendarId].reload();
    }).fail();
};