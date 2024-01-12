<div class="modal modal-blur fade" id="modal-view" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
		<form action="{{form:url:full}}" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}" data-locked-value="_formLocked">
			<div class="modal-header">
				<h5 class="modal-title">Offerte goedkeuren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-lg-3 mb-lg-3 mb-3">
						<label class="form-label" for="status">Status</label>
						<select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part:module}}/status" data-load-value="id" data-load-label="description" disabled></select>
						<div class="invalid-feedback" data-feedback-input="status"></div>
					</div>

					<div class="col-lg-3 mb-lg-3 mb-3">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" disabled></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-lg-3 mb-lg-3 mb-3">
						<label class="form-label" for="acceptorId">Goed te keuren door</label>
						<select name="acceptorId" id="acceptorId" data-load-source="{{select:url:short}}/users/acceptors" data-load-value="id" data-load-label="fullName" disabled></select>
						<div class="invalid-feedback" data-feedback-input="acceptorId"></div>
					</div>

					<div class="col-lg-3 mb-lg-3 mb-3">
						<label class="form-label" for="supplierId">Leverancier</label>
						<select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/supplier/overview" data-load-value="id" data-load-label="nameWithMainContact" disabled></select>
						<div class="invalid-feedback" data-feedback-input="acceptorId"></div>
					</div>
				</div>

				<div class="row mb-3">
					<label class="form-label" for="description">Beschrijving</label>
					<input type="text" role="tinymce" name="description" id="description" class="form-control" disabled>
					<div class="invalid-feedback" data-feedback-input="description"></div>
				</div>

				<div class="row">
					<label class="form-label">Bestellijnen</label>
					<table role="table" id="tbl{{page:id}}Line" data-source="{{table:url:full}}/line" data-no-checkbox data-small></table>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Sluiten</button>
				<button type="button" class="btn btn-success" id="btnAccept">Goedkeuren</button>
				<button type="button" class="btn btn-danger" id="btnDeny">Weigeren</button>
			</div>
		</form>
	</div>
</div>