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
					<h3 class="card-title">Patchpaneel {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label" for="schoolId">School</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="buildingId">Gebouw</label>
							<select name="buildingId" id="buildingId" data-load-source="{{select:action}}/building" data-load-value="id" data-load-label="name" data-parent-select="schoolId" required></select>
							<div class="invalid-feedback" data-feedback-input="buildingId"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label" for="roomId">Lokaal</label>
							<select name="roomId" id="roomId" data-load-source="{{select:action}}/room" data-load-value="id" data-load-label="fullNumber" data-parent-select="buildingId" required></select>
							<div class="invalid-feedback" data-feedback-input="roomId"></div>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label" for="cabinetId">Netwerkkast</label>
							<select name="cabinetId" id="cabinetId" data-load-source="{{select:action}}/cabinet" data-load-value="id" data-load-label="name" data-parent-select="roomId" required></select>
							<div class="invalid-feedback" data-feedback-input="cabinetId"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 mb-3">
							<label class="form-label" for="name">Naam</label>
							<input type="text" name="name" id="name" class="form-control" required autofocus>
							<div class="invalid-feedback" data-feedback-input="name"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="ports">Aantal patchpunten</label>
							<input type="number" name="ports" id="ports" class="form-control" min="0" max="100" step="1" required>
							<div class="invalid-feedback" data-feedback-input="ports"></div>
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