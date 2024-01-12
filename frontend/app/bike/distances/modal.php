<div class="modal modal-blur fade" id="modal-bike-distances" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Afstand</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row">
					<div class="col mb-3">
						<label class="form-label" for="alias">Alias</label>
						<input type="email" name="alias" id="alias" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="alias"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-6 col-12 mb-3">
						<label class="form-label" for="startAddressId">Start adres</label>
						<select name="startAddressId" id="startAddressId" data-load-source="{{select:url:short}}/user/address" data-load-value="id" data-load-label="formattedCurrent" required></select>
						<div class="invalid-feedback" data-feedback-input="startAddressId"></div>
					</div>

					<div class="col-lg-3 col-12 mb-3">
						<label class="form-label" for="endSchoolId">School</label>
						<select name="endSchoolId" id="endSchoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
						<div class="invalid-feedback" data-feedback-input="endSchoolId"></div>
					</div>

					<div class="col-lg-3 col-12 mb-3">
						<label for="distance" class="form-label">Afstand (km)</label>
						<input type="number" name="distance" id="distance" class="form-control" step="0.1" required>
						<div class="invalid-feedback" data-feedback-input="distance"></div>
					</div>
				</div>

				<div class="row">
					<label for="color" class="form-label">Kleur</label>
					<div class="row g-2">
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="blue" class="form-colorinput-input" checked>
								<span class="form-colorinput-color bg-blue rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="azure" class="form-colorinput-input">
								<span class="form-colorinput-color bg-azure rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="indigo" class="form-colorinput-input">
								<span class="form-colorinput-color bg-indigo rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="purple" class="form-colorinput-input">
								<span class="form-colorinput-color bg-purple rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="pink" class="form-colorinput-input">
								<span class="form-colorinput-color bg-pink rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="red" class="form-colorinput-input">
								<span class="form-colorinput-color bg-red rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="orange" class="form-colorinput-input">
								<span class="form-colorinput-color bg-orange rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="yellow" class="form-colorinput-input">
								<span class="form-colorinput-color bg-yellow rounded-circle"></span>
							</label>
						</div>
						<div class="col-auto">
							<label class="form-colorinput">
								<input name="color" type="radio" value="lime" class="form-colorinput-input">
								<span class="form-colorinput-color bg-lime rounded-circle"></span>
							</label>
						</div>
					</div>
					<div class="invalid-feedback" data-feedback-input="color"></div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze afstanden te verwijderen?</h1>
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