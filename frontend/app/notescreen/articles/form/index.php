<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<?php if (\Router\Helpers::getMethod() == "delete") : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card">
				<div class="card-body">Bent u zeker dat u de geselecteerde lijnen wilt verwijderen?</div>
				<div class="card-footer text-end">
					<button type="button" class="btn btn-success" onclick="window.history.back();">Nee</button>
					<button type="submit" class="btn btn-danger">Ja</button>
				</div>
			</form>
		<?php else : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" <?php endif; ?>>
				<div class="card-header">
					<h3 class="card-title">Artikel {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="mb-3">
						<label class="form-label" for="title">Titel</label>
						<input type="text" name="title" id="title" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="title"></div>
					</div>

					<div class="mb-3">
						<label class="form-label" for="notescreenPageId">Pagina</label>
						<select name="notescreenPageId" id="notescreenPageId" data-load-source="{{select:action}}/pages" data-load-value="id" data-load-label="name" required></select>
						<div class="invalid-feedback" data-feedback-input="notescreenPageId"></div>
					</div>

					<div class="mb-3">
						<label class="form-label" for="content">Inhoud</label>
						<textarea role="tinymce" name="content" id="content"></textarea>
						<div class="invalid-feedback" data-feedback-input="content"></div>
					</div>

					<div class="">
						<label class="form-label" for="displayTime">Toon tijd (milliseconden)</label>
						<input type="number" name="displayTime" id="displayTime" class="form-control" required min="0" max="60000">
						<div class="invalid-feedback" data-feedback-input="displayTime"></div>
					</div>
				</div>

				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>