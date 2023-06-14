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
					<h3 class="card-title">Leverancier {{page:action}}</h3>
				</div>

				<div class="card-body">
					<div class="mb-3">
						<label class="form-label" for="name">Naam</label>
						<input type="text" name="name" id="name" class="form-control" required autofocus>
						<div class="invalid-feedback" data-feedback-input="name"></div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label" for="email">E-mail</label>
							<input type="text" name="email" id="email" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="email"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="phone">Telefoon</label>
							<input type="text" name="phone" id="phone" class="form-control">
							<div class="invalid-feedback" data-feedback-input="phone"></div>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label" for="contact">Contactpersoon</label>
							<input type="text" name="contact" id="contact" class="form-control" required>
							<div class="invalid-feedback" data-feedback-input="contact"></div>
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