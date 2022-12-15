<div class="row row-cards">
	<div class="col-md-3 m-auto">
		<form class="card" action="{{form:action}}" method="POST" id="frm{{page:id}}">
			<div class="card-body">
				<div class="mb-3">
					<label for="per" class="form-label">Exporteer per</label>
					<div>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="per" checked value="school" required>
							<span class="form-check-label">School</span>
						</label>

						<label class="form-check">
							<input class="form-check-input" type="radio" name="per" value="teacher" required>
							<span class="form-check-label">Leerkracht</span>
						</label>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="school">Scholen</label>
					<select name="school[]" id="school" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" multiple required></select>
					<div class="invalid-feedback" data-feedback-input="school"></div>
				</div>

				<div class="mb-3">
					<label for="start" class="form-label">Start datum</label>
					<div class="input-icon">
						<span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
						<input role="datepicker" name="start" id="start" class="form-control" required />
					</div>
					<div class="invalid-feedback" data-feedback-input="start"></div>
				</div>

				<div class="mb-3">
					<label for="end" class="form-label">Eind datum</label>
					<div class="input-icon">
						<span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
						<input role="datepicker" name="end" id="end" class="form-control" required />
					</div>
					<div class="invalid-feedback" data-feedback-input="end"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="exportAs">Exporteren als</label>
					<div>
						<label class="form-check">
							<input class="form-check-input" type="radio" name="exportAs" checked value="xlsx" required>
							<span class="form-check-label">Excel</span>
						</label>

						<label class="form-check">
							<input class="form-check-input" type="radio" name="exportAs" value="pdf" required>
							<span class="form-check-label">PDF</span>
						</label>
					</div>
					<div class="invalid-feedback" data-feedback-input="exportAs"></div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Exporteren</button>
			</div>
		</form>
	</div>
</div>