<div class="row row-cards">
	<div class="col-md-6 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" data-prefill="{{form:action}}" data-locked-value="formLocked">
			<div class="card-header">
				<h3 class="card-title">Rechten {{page:action}}</h3>
			</div>

			<div class="card-body">
				<div class="form-group mb-3">
					<label for="moduleId" class="form-label">Module</label>
					<select class="form-control" name="moduleId" id="moduleId" data-load-source="{{select:action}}/modules" data-load-value="id" data-load-label="name" readonly></select>
					<div class="invalid-feedback" data-feedback-input="moduleId"></div>
				</div>

				<div class="form-group mb-3">
					<label for="userId" class="form-label">Gebruiker</label>
					<select class="form-control" name="userId" id="userId" data-load-source="{{select:action}}/users" data-load-value="id" data-load-label="fullName" required></select>
					<div class="invalid-feedback" data-feedback-input="user"></div>
				</div>

				<div class="form-group">
					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="view" id="view" />
						<span class="form-check-label">Bekijken</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="edit" id="edit" />
						<span class="form-check-label">Bewerken</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="export" id="export" />
						<span class="form-check-label">Exporteren</span>
					</label>

					<label class="form-check form-switch">
						<input class="form-check-input" type="checkbox" name="changeSettings" id="changeSettings" />
						<span class="form-check-label">Instellingen wijzigen</span>
					</label>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>