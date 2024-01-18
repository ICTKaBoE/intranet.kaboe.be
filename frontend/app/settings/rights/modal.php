<div class="modal modal-blur fade" id="modal-rights" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-locked-value="locked">
			<div class="modal-header">
				<h5 class="modal-title">Rechten</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="row mb-3">
					<div class="col-md-6 mb-lg-4">
						<label class="form-label" for="userId">Gebruiker</label>
						<select name="userId" id="userId" data-load-source="{{select:url:short}}/users" data-load-value="id" data-load-label="fullName" disabled required></select>
						<div class="invalid-feedback" data-feedback-input="userId"></div>
					</div>

					<div class="col-md-6 mb-lg-4">
						<label class="form-label" for="moduleIds">Modules</label>
						<select name="moduleIds" id="moduleIds" data-load-source="{{select:url:short}}/module" data-load-value="id" data-load-label="name" data-search multiple required></select>
						<div class="invalid-feedback" data-feedback-input="moduleIds"></div>
					</div>
				</div>

				<div class="row">
					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="_view" id="_view" />
						<span class="form-check-label">Bekijken</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="_edit" id="_edit" />
						<span class="form-check-label">Bewerken</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="_export" id="_export" />
						<span class="form-check-label">Exporteren</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="_changeSettings" id="_changeSettings" />
						<span class="form-check-label">Instellingen wijzigen</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="_locked" id="_locked" />
						<span class="form-check-label">Vergrendeld</span>
					</label>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>