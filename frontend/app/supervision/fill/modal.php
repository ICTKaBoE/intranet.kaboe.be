<div class="modal modal-blur fade" id="modal-fill" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Middagtoezicht</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row mb-3">
					<div class="col mb-lg-3">
						<label class="form-label mb-1" for="userMainSchoolId">School</label>
						<select name="userMainSchoolId" id="userMainSchoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:profile:mainSchoolId}}"></select>
						<div class="invalid-feedback" data-feedback-input="userMainSchoolId"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-6 mb-lg-3">
						<label for="date" class="form-label">Datum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" name="date" id="date" class="form-control" required disabled />
						</div>
						<div class="invalid-feedback" data-feedback-input="date"></div>
					</div>

					<div class="col-lg-3 mb-lg-3">
						<label for="start" class="form-label">Start uur</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-clock"></i></span>
							<input type="time" name="start" id="start" class="form-control" required disabled />
						</div>
						<div class="invalid-feedback" data-feedback-input="start"></div>
					</div>

					<div class="col-lg-3 mb-lg-3">
						<label for="end" class="form-label">Eind uur</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-clock"></i></span>
							<input type="time" name="end" id="end" class="form-control" required disabled />
						</div>
						<div class="invalid-feedback" data-feedback-input="end"></div>
					</div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze middagtoezicht te verwijderen?</h1>
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