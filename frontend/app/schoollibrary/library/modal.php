<div class="modal modal-blur fade" id="modal-library" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
			<div class="modal-header">
				<h5 class="modal-title">Schoolboek</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
						<!--data-default-value="{{user:profile:mainSchoolId}}"-->
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-md-6">
						<label class="form-label" for="author">Auteur</label>
						<input type="text" name="author" id="author" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="author"></div>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="title">Titel</label>
					<input type="text" name="title" id="title" class="form-control" required autofocus>
					<div class="invalid-feedback" data-feedback-input="title"></div>
				</div>

				<div class="row mb-3">
					<div class="col-md-6">
						<label class="form-label" for="isdn">ISDN</label>
						<input type="text" name="isdn" id="isdn" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="isdn"></div>
					</div>

					<div class="col-md-6">
						<label class="form-label" for="category">Categorie</label>
						<select name="category" id="category" data-load-source="{{select:url:short}}/{{url:part:module}}/category" data-load-value="id" data-load-label="description" required></select>
						<div class="invalid-feedback" data-feedback-input="category"></div>
					</div>
				</div>
			</div>
			<div class="modal-footer" data-form-type="create|update">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze pagina's te verwijderen?</h1>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-danger">Ja</button>
			</div>
		</form>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-library-history" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog" data-source="{{api:url}}/html/{{url:part:module}}/{{url:part:page}}">
	<div class="modal-dialog modal modal-dialog-centered modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Geschiedenis</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="list-group list-group-flush" id="libraryActionContainer"></div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
			</div>
		</div>
	</div>
</div>