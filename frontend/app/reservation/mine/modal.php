<div class="modal modal-blur fade" id="modal-reservation" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Reservatie</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row mb-3 border-bottom">
					<div class="col-md-6 mb-lg-4">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-on-change="check" required data-default-value="{{user:profile:mainSchoolId}}"></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-md-6 mb-lg-4">
						<label class="form-label" for="type">Type</label>
						<select name="type" id="type" data-on-change="check" data-load-source="{{select:url:short}}/{{url:part:module}}/type" data-load-value="id" data-load-label="description" required></select>
						<div class="invalid-feedback" data-feedback-input="type"></div>
					</div>
				</div>

				<div class="row mb-3 border-bottom">
					<div class="col mb-lg-4">
						<label class="form-label" for="assetId">Lokaal/Toestel</label>
						<select name="assetId" id="assetId" data-load-source="[computer@{{select:url:short}}/management/computer;ipad@{{select:url:short}}/management/ipad;room@{{select:url:short}}/management/rooms;laptopcart@{{select:url:short}}/management/cart;ipadcart@{{select:url:short}}/management/cart]" data-load-value="id" data-load-label="[computer@name;ipad@deviceName;room@fullNumberBuilding;laptopcart@devicelist;ipadcart@devicelist]" data-search multiple required></select>
						<div class="invalid-feedback" data-feedback-input="assetId"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label for="startDate" class="form-label">Start datum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" name="startDate" id="startDate" class="form-control" disabled required />
						</div>
						<div class="invalid-feedback" data-feedback-input="startDate"></div>
					</div>

					<div class="col-md-6">
						<label for="startTime" class="form-label">Start time</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-clock"></i></span>
							<input type="time" name="startTime" id="startTime" class="form-control" min="08:00" max="16:00" disabled required />
						</div>
						<div class="invalid-feedback" data-feedback-input="startTime"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label for="endDate" class="form-label">Eind datum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" name="endDate" id="endDate" class="form-control" disabled required />
						</div>
						<div class="invalid-feedback" data-feedback-input="endDate"></div>
					</div>

					<div class="col-md-6">
						<label for="endTime" class="form-label">End time</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-clock"></i></span>
							<input type="time" name="endTime" id="endTime" class="form-control" min="08:00" max="16:00" disabled required />
						</div>
						<div class="invalid-feedback" data-feedback-input="endTime"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer" data-form-type="create|update">
				<button type="button" class="btn btn-danger me-auto" onclick="window.delete()">Verwijderen</button>

				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze reservatie te verwijderen?</h1>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-danger">Ja</button>
			</div>
		</form>
	</div>
</div>