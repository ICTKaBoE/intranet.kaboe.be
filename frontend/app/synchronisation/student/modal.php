<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
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
						<label class="form-label" for="filterClass">Klas</label>
						<select name="filterClass" id="filterClass" data-load-source="{{select:url:short}}/class" data-load-value="id" data-load-label="name" data-parent-select="filterSchool"></select>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="button" onclick="applyFilter()" class="btn btn-primary">Filteren</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-reset" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="{{form:url:full}}/password/reset" method="post" id="frm{{page:id}}Reset" class="modal-content">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Wachtwoord resetten</h5>
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
					<button type="submit" class="btn btn-primary">Wachtwoord opnieuw instellen</button>
				</div>
			</div>
		</form>
	</div>
</div>
</div>