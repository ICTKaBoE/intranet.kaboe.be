<div class="row row-cards">
	<div class="col-md-6 m-auto">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card">
			<div class="card-header">
				<h3 class="card-title">Ticket toevoegen</h3>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-6 mb-3">
						<label class="form-label" for="schoolId">School</label>
						<select name="schoolId" id="schoolId" data-load-source="{{select:action}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:profile:mainSchoolId}}" data-on-change="check"></select>
						<div class="invalid-feedback" data-feedback-input="schoolId"></div>
					</div>

					<div class="col-lg-6 mb-3">
						<label class="form-label" for="priority">Prioriteit</label>
						<select name="priority" id="priority" data-load-source="{{select:action}}/priority" data-load-value="id" data-load-label="description" data-default-value="L"></select>
						<div class="invalid-feedback" data-feedback-input="priority"></div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-6 mb-3">
						<label class="form-label" for="type">Type</label>
						<select name="type" id="type" data-load-source="{{select:action}}/type" data-load-value="id" data-load-label="description" data-default-value="O" data-on-change="check"></select>
						<div class="invalid-feedback" data-feedback-input="type"></div>
					</div>

					<div class="col-lg-6 mb-3">
						<label class="form-label" for="subtype">Sub-type</label>
						<select name="subtype" id="subtype" data-load-source="{{select:action}}/subtype" data-load-value="id" data-load-label="description" data-default-value="O" data-on-change="check"></select>
						<div class="invalid-feedback" data-feedback-input="subtype"></div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 mb-3">
						<label class="form-label" for="deviceLocation">Lokaal/Klas</label>
						<input type="text" name="deviceLocation" id="deviceLocation" class="form-control">
						<div class="invalid-feedback" data-feedback-input="deviceLocation"></div>
					</div>

					<div class="col-lg-4 mb-3">
						<label class="form-label" for="deviceBrand">Merk</label>
						<input type="text" name="deviceBrand" id="deviceBrand" class="form-control" disabled>
						<div class="invalid-feedback" data-feedback-input="deviceBrand"></div>
					</div>

					<div class="col-lg-4 mb-3">
						<label class="form-label" for="deviceType">Type</label>
						<input type="text" name="deviceType" id="deviceType" class="form-control" disabled>
						<div class="invalid-feedback" data-feedback-input="deviceType"></div>
					</div>
				</div>

				<div class="mb-3">
					<label class="form-label" for="deviceName">Toestelnaam</label>
					<input type="text" name="deviceName" id="deviceNameCustom" class="form-control">
					<div id="deviceNameSelectWrapper" class="d-none">
						<select name="deviceName" id="deviceNameSelect" data-load-source="{{select:action}}/computer" data-load-value="name" data-load-label="name"></select>
					</div>
					<div class="invalid-feedback" data-feedback-input="deviceName"></div>
				</div>
			</div>

			<div class="card-body">
				<label class="form-label" for="content">Beschrijving probleem</label>
				<input type="text" role="tinymce" name="content" id="content" class="form-control" required>
				<div class="invalid-feedback" data-feedback-input="content"></div>
			</div>

			<div class="card-footer">
				<div class="row align-items-center">
					<div class="col">
						<label class="form-check">
							<input class="form-check-input" type="checkbox" id="new" name="new" />
							<span class="form-check-label">Nog een ticket aanmaken</span>
						</label>
					</div>

					<div class="col-auto">
						<button type="submit" class="btn btn-primary" id="btnCreate">Aanmaken</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>