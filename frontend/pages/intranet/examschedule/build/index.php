<div class="row">
    <div class="col-12 col-md-8 mx-auto mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-editable data-view="timeGridWeek" data-all-day-slot data-slot-duration="{{module:slot.duration}}" data-slot-min-time="{{module:slot.min}}" data-slot-max-time="{{module:slot.max}}" data-source="{{calendar:url:full}}" data-action="{{calendar:url:full}}" data-date-click="removeTime" data-date-select="setTime"></div>
            </div>
        </div>
    </div>
</div>