<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<?php if (\Router\Helpers::getMethod() == "delete") : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card">
				<div class="card-body">Bent u zeker dat u de geselecteerde afstand(en) wilt verwijderen?</div>
				<div class="card-footer text-end">
					<button type="button" class="btn btn-success" onclick="window.history.back();">Nee</button>
					<button type="submit" class="btn btn-danger">Ja</button>
				</div>
			</form>
		<?php else : ?>
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" <?php endif; ?>>
				<div class="card-header">
					<h3 class="card-title">Afstand {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="mb-3">
						<label class="form-label" for="alias">Alias</label>
						<input type="text" name="alias" id="alias" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="alias"></div>
					</div>

					<div class="row mb-3">
						<div class="col-7">
							<label for="startAddressId" class="form-label">Start adres</label>
							<select name="startAddressId" id="startAddressId" data-load-source="{{select:action}}/userAddress" data-load-value="id" data-load-label="formattedCurrent" required></select>
							<div class="invalid-feedback" data-feedback-input="startAddressId"></div>
						</div>

						<div class="col-3">
							<label for="endSchoolId" class="form-label">School</label>
							<select name="endSchoolId" id="endSchoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="endSchoolId"></div>
						</div>

						<div class="col-2">
							<label for="distance" class="form-label">Afstand (km)</label>
							<input type="number" name="distance" id="distance" class="form-control" step="0.1" required>
							<div class="invalid-feedback" data-feedback-input="distance"></div>
						</div>
					</div>

					<div class="row">
						<label for="color" class="form-label">Kleur</label>
						<div class="row g-2">
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="blue" class="form-colorinput-input" checked>
									<span class="form-colorinput-color bg-blue rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="azure" class="form-colorinput-input">
									<span class="form-colorinput-color bg-azure rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="indigo" class="form-colorinput-input">
									<span class="form-colorinput-color bg-indigo rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="purple" class="form-colorinput-input">
									<span class="form-colorinput-color bg-purple rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="pink" class="form-colorinput-input">
									<span class="form-colorinput-color bg-pink rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="red" class="form-colorinput-input">
									<span class="form-colorinput-color bg-red rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="orange" class="form-colorinput-input">
									<span class="form-colorinput-color bg-orange rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="yellow" class="form-colorinput-input">
									<span class="form-colorinput-color bg-yellow rounded-circle"></span>
								</label>
							</div>
							<div class="col-auto">
								<label class="form-colorinput">
									<input name="color" type="radio" value="lime" class="form-colorinput-input">
									<span class="form-colorinput-color bg-lime rounded-circle"></span>
								</label>
							</div>
						</div>
						<div class="invalid-feedback" data-feedback-input="color"></div>
					</div>
				</div>

				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>