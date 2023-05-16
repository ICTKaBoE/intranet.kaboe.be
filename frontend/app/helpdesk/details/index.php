<div class="row">
	<div class="col-md-9 mb-3">
		<div>{{helpdesk:thread}}</div>

		<form action="{{form:action}}/thread/{{url:param:id}}" method="POST" class="card" id="frm{{page:id}}Thread">
			<div class="card-header card-header-light">
				<h3 class="card-title d-block">
					Reactie toevoegen
					<span class="card-subtitle d-block">
						Het toevoegen van een reactie aan een gesloten ticket, zorgt ervoor dat het ticket automatisch terug opengezet wordt.<br />
						Wil je een nieuw probleem melden in verband met het zelfde toestel, moet je hiervoor een nieuw ticket openen.
					</span>
				</h3>
			</div>

			<div class="card-body">
				<label class="form-label" for="content">Reactie</label>
				<input type="text" role="tinymce" name="content" id="content" class="form-control" required>
				<div class="invalid-feedback" data-feedback-input="content"></div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary" id="btnAddReaction">Toevoegen</button>
			</div>
		</form>
	</div>

	<div class="col-md-3">
		<form action="{{form:action}}/details/{{url:param:id}}" method="post" class="card mb-3" id="frm{{page:id}}Details" data-prefill="{{form:action}}/details/{{url:param:id}}" data-locked-value="formLocked" data-no-reser-after-submit>
			<div class="card-header card-header-light">
				<h3 class="card-title">Details</h3>
			</div>

			<div class="card-body">
				<div class="mb-2">
					<label class="form-label mb-0" for="number">Nummer</label>
					<input type="text" name="number" id="number" class="form-control-plaintext p-0" readonly>
					<div class="invalid-feedback" data-feedback-input="number"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="schoolId">School</label>
					<select name="schoolId" id="schoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name"></select>
					<div class="invalid-feedback" data-feedback-input="schoolId"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="priority">Prioriteit</label>
					<select name="priority" id="priority" data-load-source="{{select:action}}/priority" data-load-value="id" data-load-label="description"></select>
					<div class="invalid-feedback" data-feedback-input="priority"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="status">Status</label>
					<select name="status" id="status" data-load-source="{{select:action}}/status" data-load-value="id" data-load-label="description"></select>
					<div class="invalid-feedback" data-feedback-input="status"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="type">Type</label>
					<select name="type" id="type" data-load-source="{{select:action}}/type" data-load-value="id" data-load-label="description"></select>
					<div class="invalid-feedback" data-feedback-input="type"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="subtype">Sub-type</label>
					<select name="subtype" id="subtype" data-load-source="{{select:action}}/subtype" data-load-value="id" data-load-label="description"></select>
					<div class="invalid-feedback" data-feedback-input="subtype"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-0" for="deviceLocation">Lokaal</label>
					<input type="text" name="deviceLocation" id="deviceLocation" class="form-control-plaintext p-0" readonly>
					<div class="invalid-feedback" data-feedback-input="deviceLocation"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-0" for="deviceBrand">Merk</label>
					<input type="text" name="deviceBrand" id="deviceBrand" class="form-control-plaintext p-0" readonly>
					<div class="invalid-feedback" data-feedback-input="deviceBrand"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-0" for="deviceType">Type</label>
					<input type="text" name="deviceType" id="deviceType" class="form-control-plaintext p-0" readonly>
					<div class="invalid-feedback" data-feedback-input="deviceType"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-0" for="deviceName">Toestelnaam</label>
					<input type="text" name="deviceName" id="deviceName" class="form-control-plaintext p-0" readonly>
					<div class="invalid-feedback" data-feedback-input="deviceName"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="creatorId">Aangemaakt door</label>
					<select name="creatorId" id="creatorId" data-load-source="{{select:action}}/users" data-load-value="id" data-load-label="fullName" disabled></select>
					<div class="invalid-feedback" data-feedback-input="creatorId"></div>
				</div>

				<div class="mb-2">
					<label class="form-label mb-1" for="assignedToId">Toegewezen aan</label>
					<select name="assignedToId" id="assignedToId" data-load-source="{{select:action}}/users/assignable" data-load-value="id" data-load-label="fullName"></select>
					<div class="invalid-feedback" data-feedback-input="assignedToId"></div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary" id="btnUpdateDetails">Updaten</button>
			</div>
		</form>

		<div class="card">
			<div class="card-header card-header-light">
				<h3 class="card-title">Geschiedenis</h3>
			</div>

			<div class="list-group list-group-flush">{{helpdesk:actions}}</div>
		</div>
	</div>
</div>