<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:url:full}}" method="post" data-prefill-with-url>
			<div class="card">
				<div class="card-body" id="tbl{{page:id}}">
					<div class="row mb-3">
						<div class="col">
							<label for="schoolId" class="form-label">Kies de school</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required disabled></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>
					</div>

					<div class="row mb-3">
						<div class="col">
							<label for="person" class="form-label">Uw naam</label>
							<select name="person" id="person" data-load-source="{{select:url:short}}/{{url:part:page}}/person" data-load-value="name" data-load-label="name" data-parent-select="schoolId" required></select>
							<div class="invalid-feedback" data-feedback-input="person"></div>
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-12">Temperaturen (°C)</div>

						<div class="col-md-3">
							<label for="soupTemp" class="form-label">Soep</label>
							<input type="number" step="0.1" name="soupTemp" id="soupTemp" class="form-control" />
							<div class="invalid-feedback" data-feedback-input="soupTemp"></div>
						</div>

						<div class="col-md-3">
							<label for="potatoRicePastaTemp" class="form-label">Aardappel, pasta, rijst, ...</label>
							<input type="number" step="0.1" name="potatoRicePastaTemp" id="potatoRicePastaTemp" class="form-control" />
							<div class="invalid-feedback" data-feedback-input="potatoRicePastaTemp"></div>
						</div>

						<div class="col-md-3">
							<label for="vegetablesTemp" class="form-label">Groente</label>
							<input type="number" step="0.1" name="vegetablesTemp" id="vegetablesTemp" class="form-control" />
							<div class="invalid-feedback" data-feedback-input="vegetablesTemp"></div>
						</div>

						<div class="col-md-3">
							<label for="meatFishTemp" class="form-label">Vlees/Vis</label>
							<input type="number" step="0.1" name="meatFishTemp" id="meatFishTemp" class="form-control" />
							<div class="invalid-feedback" data-feedback-input="meatFishTemp"></div>
						</div>
					</div>

					<div class="row">
						<div class="col">
							<label for="description" class="form-label">Opmerking</label>
							<input type="text" name="description" id="description" class="form-control" />
							<div class="invalid-feedback" data-feedback-input="description"></div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Opslaan</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>