<div class="row row-cards">
	<div class="col-12">
		<div class="card">
			<table role="table" id="tbl{{page:id}}" data-source="{{table:action}}"></table>
		</div>
	</div>
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
					<label for="class" class="form-label">Klas</label>
					<select name="class" id="class" data-parent-select="school" data-load-source="{{select:action}}/class" data-default-value="<?= SELECT_ALL_VALUES; ?>" data-load-value="name" data-load-label="name" required></select>
					<div class="invalid-feedback" data-feedback-input="class"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="filter()">Filter toepassen</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-{{page:id}}PasswordReset" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Wachtwoord opnieuw instellen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<label class="form-check">
						<input class="form-check-input" type="checkbox" id="random" name="random" />
						<span class="form-check-label">Random</span>
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="resetPassword()">Wachtwoord opnieuw instellen</button>
			</div>
		</div>
	</div>
</div>