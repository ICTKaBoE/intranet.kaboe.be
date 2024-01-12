<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <label for="schoolId" class="form-label">Kies jouw school</label>
                <select name="sel{{page:id}}" id="sel{{page:id}}" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-on-change="loadTable" required></select>
                <!-- data-default-value="{{user:profile:mainSchoolId}}" disabled -->
                <div class="invalid-feedback" data-feedback-input="schoolId"></div>
            </div>
        </div>
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
                <div id="cal{{page:id}}" role="calendar" data-view="timeGridWeek" data-all-day-slot data-slot-duration="02:00:00" data-slot-min-time="08:00:00" data-slot-max-time="16:00:00" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}" data-date-click="edit"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Wijzigingen aanbrengen aan de kalender in deze modus is niet mogelijk.</p>
            </div>
        </div>
    </div>
</div>


<script>
    let calendarId = "cal{{page:id}}";
</script>