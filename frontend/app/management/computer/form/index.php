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
							<select name="buildingId" id="buildingId" data-load-source="{{select:action}}/building" data-load-value="id" data-load-label="name" data-parent-select="schoolId"></select>
							<div class="invalid-feedback" data-feedback-input="buildingId"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="roomId">Lokaal</label>
							<select name="roomId" id="roomId" data-load-source="{{select:action}}/room" data-load-value="id" data-load-label="fullNumber" data-parent-select="buildingId"></select>
							<div class="invalid-feedback" data-feedback-input="roomId"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3 mb-3">
							<label class="form-label" for="type">Type</label>
							<select name="type" id="type" data-load-source="{{select:action}}/type" data-load-value="id" data-load-label="description" required></select>
							<div class="invalid-feedback" data-feedback-input="type"></div>
						</div>

						<div class="col-md-9 mb-3">
							<label class="form-label" for="name">Naam</label>
							<input type="text" name="name" id="name" class="form-control" required autofocus>
							<div class="invalid-feedback" data-feedback-input="name"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3 mb-3">
							<label class="form-label" for="osType">OS type</label>
							<select name="osType" id="osType" data-load-source="{{select:action}}/ostype" data-load-value="id" data-load-label="description"></select>
							<div class="invalid-feedback" data-feedback-input="osType"></div>
						</div>

						<div class="col-md-3 mb-3">
							<label class="form-label" for="osNumber">OS nummer</label>
							<input type="text" name="osNumber" id="osNumber" class="form-control">
							<div class="invalid-feedback" data-feedback-input="osNumber"></div>
						</div>

						<div class="col-md-3 mb-3">
							<label class="form-label" for="osBuild">OS versie</label>
							<input type="text" name="osBuild" id="osBuild" class="form-control">
							<div class="invalid-feedback" data-feedback-input="osBuild"></div>
						</div>

						<div class="col-md-3 mb-3">
							<label class="form-label" for="osArchitecture">OS architectuur</label>
							<select name="osArchitecture" id="osArchitecture" data-load-source="{{select:action}}/osarchitecture" data-load-value="id" data-load-label="description"></select>
							<div class="invalid-feedback" data-feedback-input="osArchitecture"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label" for="systemManufacturer">Merk</label>
							<input type="text" name="systemManufacturer" id="systemManufacturer" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="systemManufacturer"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="systemModel">Model</label>
							<input type="text" name="systemModel" id="systemModel" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="systemModel"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label" for="systemMemory">Geheugen</label>
							<input type="text" name="systemMemory" id="systemMemory" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemMemory"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="systemDrive">Opslag</label>
							<input type="text" name="systemDrive" id="systemDrive" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemDrive"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="systemSerialnumber">Serienummer</label>
							<input type="text" name="systemSerialnumber" id="systemSerialnumber" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemSerialnumber"></div>
						</div>

						<div class="col-md-8 mb-3">
							<label class="form-label" for="systemProcessor">Processor</label>
							<input type="text" name="systemProcessor" id="systemProcessor" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemProcessor"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="systemBiosManufacturer">BIOS Merk</label>
							<input type="text" name="systemBiosManufacturer" id="systemBiosManufacturer" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemBiosManufacturer"></div>
						</div>

						<div class="col-md-8 mb-3">
							<label class="form-label" for="systemBiosVersion">BIOS Versie</label>
							<input type="text" name="systemBiosVersion" id="systemBiosVersion" class="form-control">
							<div class="invalid-feedback" data-feedback-input="systemBiosVersion"></div>
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