<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card mb-3">
			<div class="card-header">
				<h4 class="card-title">Mededeling</h4>
			</div>

			<div class="card-body">
				<p>
					Bij wijziging van afstanden, moeten alle fietsdagen van vóór de wijziging ingevoerd zijn.<br />
					Pas daarna pas je je afstanden aan.
				</p>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Legenda</h4>
			</div>

			<div class="card-body">{{bike:homework:distances}}</div>
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
				<div id="cal{{page:id}}" role="calendar" data-source="[{{calendar:action}},{{api:url}}/calendar/holliday]" data-action="{{form:action}}" data-date-click="setRide" data-range-start="{{homework:range:start}}" data-range-end="{{homework:range:end}}"></div>
			</div>
		</div>
	</div>

	<div class="col-md-3">
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