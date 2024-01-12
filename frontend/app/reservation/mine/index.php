<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Legenda</h4>
            </div>

            <div class="card-body">{{fillcalendar:legenda}}</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-2 bg-blue"></div>
                    <div class="col">Feestdag / Schoolvakantie</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-view="timeGridWeek" data-all-day-slot data-slot-duration="02:00:00" data-slot-min-time="08:00:00" data-slot-max-time="16:00:00" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}" data-date-click="edit" data-date-select="add" data-range-start="{{fillcalendar:range:start}}" data-range-end="{{fillcalendar:range:end}}"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Selecteer, een periode, waarin je een reservatie wil maken.</p>
                <p>Je kan zoveel klikken en selecteren als je wil.</p>
                <p>Om een reservatie te verwijderen, klik op de reservatie en klik op verwijderen.</p>
                <p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let calendarId = "cal{{page:id}}";
</script>