<div class="modal modal-blur fade" id="modal-helpdesk-ticket-filter" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filteren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-12 mb-3">
						<label class="form-label" for="filterSchool">School</label>
						<select name="filterSchool" id="filterSchool" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name"></select>
					</div>

					<div class="col-12 mb-3">
						<label class="form-label" for="filterStatus">Status</label>
						<select name="filterStatus" id="filterStatus" data-load-source="{{select:url:short}}/{{url:part:module}}/status" data-load-value="id" data-load-label="description"></select>
					</div>

					<div class="col-12 mb-3">
						<label class="form-label" for="filterPriority">Prioriteit</label>
						<select name="filterPriority" id="filterPriority" data-load-source="{{select:url:short}}/{{url:part:module}}/priority" data-load-value="id" data-load-label="description"></select>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="button" onclick="emptyFilter()" class="btn btn-primary">Filter Legen</button>
				<button type="button" onclick="applyFilter()" class="btn btn-primary">Filteren</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-helpdesk-ticket-view" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog" data-source="{{api:url}}/html/helpdesk/details">
	<div class="modal-dialog modal-fullscreen modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Ticket bekijken</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body row">
				<div class="col-md-9 mb-3">
					<form action="{{form:url:short}}/{{url:part:module}}/thread" method="POST" class="card mb-3" id="frm{{page:id}}Thread">
						<input type="hidden" name="id" id="id" />
						<div class="card-header card-header-light">
							<h3 class="card-title d-block">
								Reactie toevoegen
								<span class="card-subtitle d-block">
									Het toevoegen van een reactie aan een gesloten ticket, zorgt ervoor dat het ticket automatisch terug opengezet wordt.
								</span>
							</h3>
						</div>

						<div class="card-body">
							<input type="text" role="tinymce" name="content" id="content" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="content"></div>
						</div>

						<div class="card-footer text-end">
							<button type="submit" class="btn btn-primary" id="btnAddReaction">Reageren</button>
						</div>
					</form>

					<div id="helpdeskThreadContainer"></div>
				</div>

				<div class="col-md-3">
					<form action="{{form:url:short}}/{{url:part:module}}/details" method="post" class="card mb-3" id="frm{{page:id}}Details" data-locked-value="formLocked" data-no-reser-after-submit>
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
								<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name"></select>
								<div class="invalid-feedback" data-feedback-input="schoolId"></div>
							</div>

							<div class="mb-2">
								<label class="form-label mb-1" for="priority">Prioriteit</label>
								<select name="priority" id="priority" data-load-source="{{select:url:short}}/{{url:part:module}}/priority" data-load-value="id" data-load-label="description"></select>
								<div class="invalid-feedback" data-feedback-input="priority"></div>
							</div>

							<div class="mb-2">
								<label class="form-label mb-1" for="status">Status</label>
								<select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part:module}}/status" data-load-value="id" data-load-label="description"></select>
								<div class="invalid-feedback" data-feedback-input="status"></div>
							</div>

							<div class="mb-2">
								<label class="form-label mb-1" for="type">Type</label>
								<select name="type" id="type" data-load-source="{{select:url:short}}/{{url:part:module}}/type" data-load-value="id" data-load-label="description"></select>
								<div class="invalid-feedback" data-feedback-input="type"></div>
							</div>

							<div class="mb-2">
								<label class="form-label mb-1" for="subtype">Sub-type</label>
								<select name="subtype" id="subtype" data-load-source="{{select:url:short}}/{{url:part:module}}/subtype" data-load-value="id" data-load-label="description"></select>
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
								<select name="creatorId" id="creatorId" data-load-source="{{select:url:short}}/users" data-load-value="id" data-load-label="fullName" disabled></select>
								<div class="invalid-feedback" data-feedback-input="creatorId"></div>
							</div>

							<div class="mb-2">
								<label class="form-label mb-1" for="assignedToId">Toegewezen aan</label>
								<select name="assignedToId" id="assignedToId" data-load-source="{{select:url:short}}/users/assignable" data-load-value="id" data-load-label="fullName"></select>
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

						<div class="list-group list-group-flush" id="helpdeskActionContainer"></div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Sluiten</button>
			</div>
		</div>
	</div>
</div>