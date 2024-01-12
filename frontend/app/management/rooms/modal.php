<div class="modal modal-blur fade" id="modal-management-rooms" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Lokaal</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row">
					<div class="col-md-6 mb-3">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-md-6 mb-3">
						<label class="form-label" for="buildingId">Gebouw</label>
						<select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/{{url:part:module}}/building" data-load-value="id" data-load-label="name" data-parent-select="schoolId" required></select>
						<div class="invalid-feedback" data-feedback-input="buildingId"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 mb-3">
						<label class="form-label" for="floor">Verdiep</label>
						<input type="number" name="floor" id="floor" class="form-control" min="0" max="3" step="1" required autofocus>
						<div class="invalid-feedback" data-feedback-input="floor"></div>
					</div>

					<div class="col-md-6 mb-3">
						<label class="form-label" for="number">Nummer</label>
						<input type="number" name="number" id="number" class="form-control" min="0" max="100" step="1" required>
						<div class="invalid-feedback" data-feedback-input="number"></div>
					</div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze lokalen te verwijderen?</h1>
			</div>

			<div class="modal-footer" data-form-type="create|update">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-danger">Ja</button>
			</div>
		</form>
	</div>
</div>