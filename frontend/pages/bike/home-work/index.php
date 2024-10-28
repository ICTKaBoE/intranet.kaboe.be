<?php
const LIST_TEMPLATE = "  <div class='row mb-1 bg-@color@'>
								<div class='text-@textColor@'>@alias@ <i>(@formatted.distance@)</i></div>
							</div>";
?>

<div class="row">
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}" data-weekends data-date-click="setRide"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Legenda</h4>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/distance" data-template="<?= LIST_TEMPLATE; ?>" data-extra="[type=HW]"></div>
            <div class="card-body">
                <div class="row mb-1 bg-blue">
                    <div class="text-white">Feestdag / Schoolvakantie</div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Mededeling</h4>
            </div>

            <div class="card-body">
                <p>
                    Bij wijziging van school, moeten alle fietsdagen van vóór de wijziging ingevoerd zijn.<br />
                    Pas daarna pas je je afstanden aan.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Klik op de dag waarop je met de fiets kwam.</p>
                <p>
                    Je kan zoveel klikken als je wil, de kleur die overeenkomt met jou ingestelde rit, is de rit die opgeslagen wordt.<br />
                    Zie je geen kleur meer, dan heb je die dag uitgeschakeld.
                </p>
                <p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
            </div>
        </div>
    </div>
</div>

<script>
    let calendarId = "cal{{page:id}}";
</script>