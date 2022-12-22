<div class="row row-cards">
	<div class="col-md-6 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" data-locked-value="formLocked" <?php endif; ?>>
			<div class="card-header">
				<h3 class="card-title">Rijksregister {{page:action}}</h3>
			</div>

			<div class="card-body">
				<label for="" class="form-label">Kind</label>
				<fieldset class="mb-3 form-fieldset">
					<div class="row">
						<div class="col-md-9">
							<label class="form-label" for="childName">Naam</label>
							<input type="text" name="childName" id="childName" class="form-control" readonly>
							<div class="invalid-feedback" data-feedback-input="childName"></div>
						</div>

						<div class="col-md-3">
							<label class="form-label" for="childInsz">Rijksregister</label>
							<input type="text" name="childInsz" id="childInsz" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="childInsz"></div>
						</div>
					</div>
				</fieldset>

				<label for="" class="form-label">Moeder</label>
				<fieldset class="mb-3 form-fieldset">
					<div class="row">
						<div class="col-md-9">
							<label class="form-label" for="motherName">Naam</label>
							<input type="text" name="motherName" id="motherName" class="form-control" readonly>
							<div class="invalid-feedback" data-feedback-input="motherName"></div>
						</div>

						<div class="col-md-3">
							<label class="form-label" for="motherInsz">Rijksregister</label>
							<input type="text" name="motherInsz" id="motherInsz" class="form-control">
							<div class="invalid-feedback" data-feedback-input="motherInsz"></div>
						</div>
					</div>
				</fieldset>

				<label for="" class="form-label">Vader</label>
				<fieldset class="mb-3 form-fieldset">
					<div class="row">
						<div class="col-md-9">
							<label class="form-label" for="fatherName">Naam</label>
							<input type="text" name="fatherName" id="fatherName" class="form-control" readonly>
							<div class="invalid-feedback" data-feedback-input="fatherName"></div>
						</div>

						<div class="col-md-3">
							<label class="form-label" for="fatherInsz">Rijksregister</label>
							<input type="text" name="fatherInsz" id="fatherInsz" class="form-control">
							<div class="invalid-feedback" data-feedback-input="fatherInsz"></div>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>