<div class="modal modal-blur fade" id="modal-management-ipad" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Ipad</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="update">
                <div class="row">
					<div class="col-md-4 mb-3">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" disabled></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-md-4 mb-3">
						<label class="form-label" for="deviceName">Naam</label>
						<input type="text" name="deviceName" id="deviceName" class="form-control" disabled>
						<div class="invalid-feedback" data-feedback-input="deviceName"></div>
					</div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="cartId">Ipadkar</label>
                        <select name="cartId" id="cartId" data-load-source="{{select:url:short}}/{{url:part:module}}/cart" data-load-value="id" data-load-label="name" data-parent-select="schoolId"></select>
                        <div class="invalid-feedback" data-feedback-input="cartId"></div>
                    </div>
				</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="serialNumber" class="form-label">Serienummer</label>
                        <input type="text" name="serialNumber" id="serialNumber" class="form-control" disabled>
                        <div class="invalid-feedback" data-feedback-input="serialNumber"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="modelName" class="form-label">Modelnaam</label>
                        <input type="text" name="modelName" id="modelName" class="form-control" disabled>
                        <div class="invalid-feedback" data-feedback-input="modelName"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="osDescription">OS</label>
                        <input type="text" name="osDescription" id="osDescription" class="form-control" disabled>
                        <div class="invalid-feedback" data-feedback-input="osDescription"></div>
                    </div>
                    <div class="col-md-9 mb-3">
                        <label for="udId">UDID</label>
                        <input type="text" name="udId" id="udId" class="form-control" disabled>
                        <div class="invalid-feedback" data-feedback-input="udId"></div>
                    </div>
                </div>
            </div>

			<div class="modal-footer" data-form-type="update">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>