<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<?php if (\Router\Helpers::getMethod() == "delete") : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card">
				<div class="card-body">Bent u zeker dat u de geselecteerde items wilt verwijderen?</div>
				<div class="card-footer text-end">
					<button type="button" class="btn btn-success" onclick="window.history.back();">Nee</button>
					<button type="submit" class="btn btn-danger">Ja</button>
				</div>
			</form>
		<?php else : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" <?php endif; ?>>
				<input type="text" name="icon" id="icon" hidden />
				<div class="card-header">
					<h3 class="card-title">Start Icoon {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="type">Type</label>
							<select name="type" id="type" data-load-source="{{select:action}}/type" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="type"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="name">Naam</label>
							<input type="text" name="name" id="name" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="name"></div>
						</div>

						<div class="col-md-2 mb-3">
							<label class="form-label" for="width">Breedte (x/12)</label>
							<input type="number" min="1" max="12" name="width" id="width" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="width"></div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label" for="url">Link</label>
						<input type="text" name="url" id="url" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="url"></div>
					</div>

					<div class="mb-3">
						<img id="iconPreview" />
					</div>
				</div>

				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>