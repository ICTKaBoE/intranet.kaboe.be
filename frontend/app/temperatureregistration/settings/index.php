<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card" data-prefill>
			<div class="card-body">
				<h3>Namen die mogen invoeren</h3>
				<div class="row mb-3">
					<div class="col-12">
						<label for="names1" class="form-label">De Meidoorn</label>
						<textarea name="names1" id="names1" rows="3" class="form-control"></textarea>
						<div class="invalid-feedback" data-feedback-input="names1"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<label for="names2" class="form-label">De Wegel</label>
						<textarea name="names2" id="names2" rows="3" class="form-control"></textarea>
						<div class="invalid-feedback" data-feedback-input="names2"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<label for="names3" class="form-label">Sint-Antonius</label>
						<textarea name="names3" id="names3" rows="3" class="form-control"></textarea>
						<div class="invalid-feedback" data-feedback-input="names3"></div>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">
						<label for="names4" class="form-label">Sint-Jozef</label>
						<textarea name="names4" id="names4" rows="3" class="form-control"></textarea>
						<div class="invalid-feedback" data-feedback-input="names4"></div>
					</div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>