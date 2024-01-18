<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <label for="schoolId" class="form-label">Kies jouw school</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-on-change="check" data-default-value="{{user:profile:mainSchoolId}}"></select>
                <div class="invalid-feedback" data-feedback-input="schoolId"></div>
            </div>
            <div class="card-body">
                <label for="type" class="form-label">Kies jouw type</label>
                <select name="type" id="type" data-load-source="{{select:url:short}}/{{url:part:module}}/type" data-load-value="id" data-load-label="description" data-on-change="check"></select>
                <div class="invalid-feedback" data-feedback-input="type"></div>
            </div>
            <div class="card-body">
                <label for="assetId" class="form-label">Kies jouw lokaal/toestel</label>
                <select name="assetId" id="assetId" data-load-source="[computer@{{select:url:short}}/management/computer;ipad@{{select:url:short}}/management/ipad;room@{{select:url:short}}/management/rooms;laptopcart@{{select:url:short}}/management/cart;ipadcart@{{select:url:short}}/management/cart]" data-load-value="id" data-load-label="[computer@name;ipad@deviceName;room@fullNumberBuilding;laptopcart@devicelist;ipadcart@devicelist]" data-search data-on-change="loadCalender"></select>
                <div class="invalid-feedback" data-feedback-input="assetId"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-view="timeGridWeek" data-slot-duration="02:00:00" data-slot-min-time="08:00:00" data-slot-max-time="16:00:00" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Op deze kalender zijn de reservaties per lokaal/toestel te zien.</p>
                <p>Aan de linkerzijde vind u een formulier waar enkele details moeten ingevuld worden alvorens iets te zien op de kalender.</p>
                <p>De beschikbare momenten vallen direct op en kunnen via het tablad "Mijn reservaties" gereserveerd worden.</p>
                <p>Bij laptops en ipads worden ook de bijhorende karreservaties weergegeven wanneer deze zich in een laptopkar/ipadkar bevinden.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let calendarId = "cal{{page:id}}";
</script>