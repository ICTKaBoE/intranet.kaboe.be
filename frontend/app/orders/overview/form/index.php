<div class="row row-cards">
	<?php if (\Router\Helpers::getMethod() == "delete") : ?>
		<div class="col-md-5 m-auto">
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card">
				<div class="card-body">Bent u zeker dat u de geselecteerde lijnen wilt verwijderen?</div>
				<div class="card-footer text-end">
					<button type="button" class="btn btn-success" onclick="window.history.back();">Nee</button>
					<button type="submit" class="btn btn-danger">Ja</button>
				</div>
			</form>
		</div>
	<?php else : ?>
		<div class="col-md-8 m-auto">
			<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" <?php if (\Router\Helpers::getMethod() == "edit") : ?>data-prefill="{{form:action}}" <?php endif; ?>>
				<div class="card-header">
					<h3 class="card-title">Bestelling {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label mb-1" for="schoolId">School</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label mb-1" for="supplierId">Leverancier</label>
							<select name="supplierId" id="supplierId" data-load-source="{{select:action}}/supplier" data-load-value="id" data-load-label="nameWithContact" required></select>
							<div class="invalid-feedback" data-feedback-input="supplierId"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label mb-1" for="acceptorId">Goed te keuren door</label>
							<select name="acceptorId" id="acceptorId" data-load-source="{{select:action}}/users" data-load-value="id" data-load-label="fullName"></select>
							<div class="invalid-feedback" data-feedback-input="acceptorId"></div>
						</div>
					</div>
				</div>

				<div class="card-body p-0">
					<table id="tbl{{page:id}}" data-source="{{table:action}}" role="table" data-small></table>
				</div>

				<div class="card-footer text-end">
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div>