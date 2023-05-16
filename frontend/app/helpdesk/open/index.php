<div class="card">
	<table role="table" id="tbl{{page:id}}" data-source="{{table:action}}" data-double-click="showDetails" data-auto-refresh="60"></table>
</div>

<div class="modal modal-blur fade" id="modal-{{page:id}}" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filteren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="row mb-3">
					<label for="school" class="form-label">School</label>
					<select name="school" id="school" data-load-source="{{select:action}}/school" data-default-value="{{user:profile:mainSchoolId}}" data-load-value="id" data-load-label="name" required></select>
					<div class="invalid-feedback" data-feedback-input="school"></div>
				</div>

				<div class="row mb-3">
					<label for="creator" class="form-label">Aangemaakt door</label>
					<select name="creator" id="creator" data-load-source="{{select:action}}/users" data-load-value="id" data-load-label="fullName" required></select>
					<div class="invalid-feedback" data-feedback-input="creator"></div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn" data-bs-dismiss="modal" onclick="clearFilter()">Filter legen</button>
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="filter()">Filter toepassen</button>
			</div>
		</div>
	</div>
</div>