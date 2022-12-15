<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" data-prefill="{{form:action}}">
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-6">
						<label class="form-label" for="userName">Naam</label>
						<input type="text" name="userName" id="userName" class="form-control" readonly>
						<div class="invalid-feedback" data-feedback-input="userName"></div>
					</div>

					<div class="col-6">
						<label class="form-label" for="userFirstName">Voornaam</label>
						<input type="text" name="userFirstName" id="userFirstName" class="form-control" readonly>
						<div class="invalid-feedback" data-feedback-input="userFirstName"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-4">
						<label for="mainSchoolId" class="form-label">Hoofdschool</label>
						<select name="mainSchoolId" id="mainSchoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" required></select>
						<div class="invalid-feedback" data-feedback-input="mainSchoolId"></div>
					</div>

					<div class="col-8">
						<label for="bankAccount" class="form-label">Rekeningnummer</label>
						<input type="text" name="bankAccount" id="bankAccount" class="form-control" data-mask="aa00 0000 0000 0000" data-mask-visible="true" required>
						<div class="invalid-feedback" data-feedback-input="bankAccount"></div>
					</div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>