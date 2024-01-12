<div class="modal modal-blur fade" id="modal-management-accesspoint" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Access Point</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">

				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="buildingId">Gebouw</label>
						<select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/{{url:part:module}}/building" data-load-value="id" data-load-label="name" data-parent-select="schoolId" required></select>
						<div class="invalid-feedback" data-feedback-input="buildingId"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="roomId">Lokaal</label>
						<select name="roomId" id="roomId" data-load-source="{{select:url:short}}/{{url:part:module}}/rooms" data-load-value="id" data-load-label="fullNumber" data-parent-select="buildingId" required></select>
						<div class="invalid-feedback" data-feedback-input="roomId"></div>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="name">Naam</label>
					<input type="text" name="name" id="name" class="form-control" required autofocus>
					<div class="invalid-feedback" data-feedback-input="name"></div>
				</div>

				<div class="row">
					<div class="col-md-6 mb-3">
						<label class="form-label" for="serialnumber">Serienummer</label>
						<input type="text" name="serialnumber" id="serialnumber" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="serialnumber"></div>
					</div>

					<div class="col-md-6 mb-3">
						<label class="form-label" for="macaddress">MAC adres</label>
						<input type="text" name="macaddress" id="macaddress" class="form-control" data-mask="**:**:**:**:**:**" data-mask-visible="true" required>
						<div class="invalid-feedback" data-feedback-input="macaddress"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label" for="brand">Merk</label>
						<input type="text" name="brand" id="brand" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="brand"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="model">Model</label>
						<input type="text" name="model" id="model" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="model"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="firmware">Firmware</label>
						<input type="text" name="firmware" id="firmware" class="form-control">
						<div class="invalid-feedback" data-feedback-input="firmware"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label" for="ip">IP adres</label>
						<input type="text" name="ip" id="ip" class="form-control" data-mask="0[00].0[00].0[00].0[00][:0000]" data-mask-visible="true" required>
						<div class="invalid-feedback" data-feedback-input="ip"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="username">Gebruikersnaam</label>
						<input type="text" name="username" id="username" class="form-control">
						<div class="invalid-feedback" data-feedback-input="username"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="password">Wachtwoord</label>
						<input type="password" name="password" id="password" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="password"></div>
					</div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze acces points te verwijderen?</h1>
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