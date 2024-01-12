<div class="modal modal-blur fade" id="modal-meals-filter" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
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
						<select name="filterSchool" id="filterSchool" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
					</div>
				</div>
				<div class="row">
					<div class="col-6 mb-3">
						<label for="filterStart" class="form-label">Start datum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" name="filterStart" id="filterStart" class="form-control" required />
						</div>
					</div>
					<div class="col-6 mb-3">
						<label for="filterEnd" class="form-label">Eind datum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" name="filterEnd" id="filterEnd" class="form-control" required />
						</div>
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