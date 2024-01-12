<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card" data-prefill>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-6">
						<label class="form-label" for="lastPayDate">Laatste uitbetalingsdatum</label>
						<div class="input-icon">
							<span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
							<input role="datepicker" class="form-control" id="lastPayDate" name="lastPayDate" required>
						</div>
						<div class="invalid-feedback" data-feedback-input="lastPayDate"></div>
					</div>
				</div>

				<div class="row mb-1">
					<div class="col">
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="blockPastOnLastPayDate" name="blockPastOnLastPayDate">
							<span class="form-check-label">Blokkeer registraties voorbij deze datum (indien dichter dan onderstaand opgegeven)</span>
						</label>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="row mb-1">
					<div class="col-6">
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="blockPast" name="blockPast">
							<span class="form-check-label">Blokkeer registraties in het verleden</span>
						</label>
					</div>

					<div class="col-6">
						<label class="form-check form-switch">
							<input class="form-check-input" type="checkbox" id="blockFuture" name="blockFuture">
							<span class="form-check-label">Blokkeer registraties in de toekomst</span>
						</label>
					</div>
				</div>

				<div class="row mb-3">
					<label class="form-label col-6" for="blockPastAmount">Aantal</label>
					<label class="form-label col-6" for="blockFutureAmount">Aantal</label>

					<div class="col-2">
						<input class="form-control" type="number" name="blockPastAmount" id="blockPastAmount" />
						<div class="invalid-feedback" data-feedback-input="blockPastAmount"></div>
					</div>

					<div class="col-4">
						<select name="blockPastType" id="blockPastType">
							<option value="d">dag</option>
							<option value="w">week</option>
							<option value="m">maand</option>
							<option value="y">jaar</option>
						</select>
						<div class="invalid-feedback" data-feedback-input="blockPastType"></div>
					</div>

					<div class="col-2">
						<input class="form-control" type="number" name="blockFutureAmount" id="blockFutureAmount" />
						<div class="invalid-feedback" data-feedback-input="blockFutureAmount"></div>
					</div>

					<div class="col-4">
						<select name="blockFutureType" id="blockFutureType">
							<option value="d">dag</option>
							<option value="w">week</option>
							<option value="m">maand</option>
							<option value="y">jaar</option>
						</select>
						<div class="invalid-feedback" data-feedback-input="blockFutureType"></div>
					</div>
				</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>