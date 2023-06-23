<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" data-prefill="{{form:action}}">
			<div class="card-body">
				<div class="col-12 mb-3">
					<label class="form-label" for="syncToAdFrom">Synchroniseer vanaf graad</label>
					<input type="number" min="-1" max="3" step="1" class="form-control" id="syncToAdFrom" name="syncToAdFrom" required>
					<div class="invalid-feedback" data-feedback-input="syncToAdFrom"></div>
				</div>

				<div class="col-12">
					<label class="form-label" for="dictionary">Woordenboek</label>
					<textarea name="dictionary" id="dictionary" rows="10" class="form-control"></textarea>
					<div class="invalid-feedback" data-feedback-input="dictionary"></div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>