<form action="{{form:url:short}}/user/profile" data-prefill method="post" autocomplete="off" id="frm{{page:id}}" class="card col-lg-6 col-12 m-auto">
	<div class="card-body">
		<div class="row">
			<div class="col-lg-6 col-12 mb-3">
				<label for="user.name" class="form-label">Naam</label>
				<input type="text" name="user.name" id="user.name" class="form-control" disabled/>
				<div class="invalid-feedback" data-feedback-input="user.name"></div>
			</div>

			<div class="col-lg-6 col-12 mb-3">
				<label for="user.firstName" class="form-label">Voornaam</label>
				<input type="text" name="user.firstName" id="user.firstName" class="form-control" disabled/>
				<div class="invalid-feedback" data-feedback-input="user.firstName"></div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-12 mb-3">
				<label for="mainSchoolId" class="form-label">Hoofdschool</label>
				<select role="select" class="form-control" name="mainSchoolId" id="mainSchoolId" data-load-source="{{api:url}}/select/school" data-load-value="id" data-load-label="name" disabled></select>
				<div class="invalid-feedback" data-feedback-input="mainSchoolId"></div>
			</div>

			<div class="col-lg-9 col-12 mb-3">
				<label for="bankAccount" class="form-label">Rekeningnummer</label>
				<input type="text" name="bankAccount" id="bankAccount" class="form-control" data-mask="aa00 0000 0000 0000" data-mask-visible="true" disabled/>
				<div class="invalid-feedback" data-feedback-input="bankAccount"></div>
			</div>
		</div>
	</div>
</form>