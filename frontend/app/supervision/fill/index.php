<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Legenda</h4>
			</div>

			<div class="card-body">
				<div class="row mb-1">
					<div class="col-2 bg-green"></div>
					<div class="col">Geregistreerde toezicht</div>
				</div>
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
				<div id="cal{{page:id}}" role="calendar" data-view="timeGridWeek" data-slot-duration="00:05:00" data-source="[{{calendar:action}},{{api:url}}/calendar/holliday]" data-action="{{form:action}}" data-date-click="removeTime" data-date-select="setTime" data-range-start="{{fill:range:start}}" data-range-end="{{fill:range:end}}"></div>
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

<div class="modal modal-blur fade" id="modal-{{page:id}}" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Middagtoezicht invullen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="save()">Opslaan</button>
			</div>
		</div>
	</div>
</div>

<script>
	let calendarId = "cal{{page:id}}";
</script>