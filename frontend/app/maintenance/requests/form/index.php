<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" <?php endif; ?>>
			<div class="card-header">
				<h3 class="card-title">Aanvraag {{page:action}}</h3>
			</div>

			<div class="card-body">
				<div class="mb-3">
					<label class="form-label" for="subject">Onderwerp</label>
					<input type="text" name="subject" id="subject" class="form-control" required>
					<div class="invalid-feedback" data-feedback-input="subject"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="priority">Prioriteit</label>
					<select name="priority" id="priority" required>
						<option value="low">Laag</option>
						<option value="medium">Gemiddeld</option>
						<option value="high">Hoog</option>
					</select>
					<div class="invalid-feedback" data-feedback-input="priority"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="location">Locatie</label>
					<input type="text" name="location" id="location" class="form-control">
					<div class="invalid-feedback" data-feedback-input="location"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="details">Beschrijving</label>
					<textarea name="details" id="details" rows="5" class="form-control"></textarea>
					<div class="invalid-feedback" data-feedback-input="details"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="executeBy">Uit te voeren door</label>
					<input type="text" name="executeBy" id="executeBy" class="form-control">
					<div class="invalid-feedback" data-feedback-input="executeBy"></div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="finishedByDate">Afgewerkt tegen</label>
					<div class="input-icon">
						<span class="input-icon-addon"><?= Helpers\Icon::load('calendar'); ?></span>
						<input role="datepicker" class="form-control" id="finishedByDate" name="finishedByDate">
					</div>
					<div class="invalid-feedback" data-feedback-input="finishedByDate"></div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>