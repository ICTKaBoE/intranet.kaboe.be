<?php
const LIST_TEMPLATE = "  <div class='row mb-1' style='background-color: @color@'>
								<div class='text-auto'>@name@</div>
							</div>";
?>

<div class="row">
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-editable data-view="timeGridWeek" data-all-day-slot data-slot-duration="{{module:slot.duration}}" data-slot-min-time="{{module:slot.min}}" data-slot-max-time="{{module:slot.max}}" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}" data-date-click="removeTime" data-date-select="setTime"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Legenda</h4>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}" data-source="{{list:url:short}}/school" data-template="<?= LIST_TEMPLATE; ?>"></div>
            <div class="card-body">
                <div class="row mb-1 bg-blue">
                    <div class="text-white">Feestdag / Schoolvakantie</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Klik, of selecteer, een periode, waarin je toezicht deed.</p>
                <p>Je kan zoveel klikken en selecteren als je wil.</p>
                <p>Om een toezicht te verwijderen, dan klik je op het toezicht.</p>
                <p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let calendarId = "cal{{page:id}}";
</script>