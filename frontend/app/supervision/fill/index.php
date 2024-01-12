<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Legenda</h4>
			</div>

			<div class="card-body">{{fill:legenda}}</div>

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
				<div id="cal{{page:id}}" role="calendar" data-editable data-view="timeGridWeek" data-all-day-slot data-slot-duration="{{module:slotDuration}}" data-slot-min-time="{{module:slotMinTime}}" data-slot-max-time="{{module:slotMaxTime}}" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-action="{{calendar:url:full}}" data-date-click="removeTime" data-date-select="setTime" data-range-start="{{fill:range:start}}" data-range-end="{{fill:range:end}}"></div>
			</div>
		</div>
	</div>

	<div class="col-md-3">
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