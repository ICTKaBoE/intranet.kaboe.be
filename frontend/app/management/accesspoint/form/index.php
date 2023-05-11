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
					<h3 class="card-title">AccessPoint {{page:action}}</h3>
				</div>

				<div class="card-body">

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="schoolId">School</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="buildingId">Gebouw</label>
							<select name="buildingId" id="buildingId" data-load-source="{{select:action}}/building" data-load-value="id" data-load-label="name" data-parent-select="schoolId" required></select>
							<div class="invalid-feedback" data-feedback-input="buildingId"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="roomId">Lokaal</label>
							<select name="roomId" id="roomId" data-load-source="{{select:action}}/room" data-load-value="id" data-load-label="fullNumber" data-parent-select="buildingId" required></select>
							<div class="invalid-feedback" data-feedback-input="roomId"></div>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label" for="name">Naam</label>
						<input type="text" name="name" id="name" class="form-control" required autofocus>
						<div class="invalid-feedback" data-feedback-input="name"></div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label" for="serialnumber">Serienummer</label>
							<input type="text" name="serialnumber" id="serialnumber" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="serialnumber"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="macaddress">MAC adres</label>
							<input type="text" name="macaddress" id="macaddress" class="form-control" data-mask="**:**:**:**:**:**" data-mask-visible="true" required>
							<div class="invalid-feedback" data-feedback-input="macaddress"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="brand">Merk</label>
							<input type="text" name="brand" id="brand" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="brand"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="model">Model</label>
							<input type="text" name="model" id="model" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="model"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="firmware">Firmware</label>
							<input type="text" name="firmware" id="firmware" class="form-control">
							<div class="invalid-feedback" data-feedback-input="firmware"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="ip">IP adres</label>
							<input type="text" name="ip" id="ip" class="form-control" data-mask="0[00].0[00].0[00].0[00][:0000]" data-mask-visible="true" required>
							<div class="invalid-feedback" data-feedback-input="ip"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="username">Gebruikersnaam</label>
							<input type="text" name="username" id="username" class="form-control">
							<div class="invalid-feedback" data-feedback-input="username"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="password">Wachtwoord</label>
							<input type="password" name="password" id="password" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="password"></div>
						</div>
					</div>
				</div>

				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>