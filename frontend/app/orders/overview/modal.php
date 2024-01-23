<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filteren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-12 mb-3">
						<label class="form-label" for="filterSchool">School</label>
						<select name="filterSchool" id="filterSchool" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name"></select>
					</div>

					<div class="col-12 mb-3">
						<label class="form-label" for="filterStatus">Status</label>
						<select name="filterStatus" id="filterStatus" data-load-source="{{select:url:short}}/{{url:part:module}}/status" data-load-value="id" data-load-label="description"></select>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="button" onclick="emptyFilter()" class="btn btn-primary">Filter Legen</button>
				<button type="button" onclick="applyFilter()" class="btn btn-primary">Filteren</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-order" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable" role="document">
		<form action="{{form:url:full}}" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}" data-action-field="faction" data-locked-value="_formLocked">
			<div class="modal-header">
				<h5 class="modal-title">Bestelling</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div data-step="1" data-step-title="Algemene informatie">
					<div class="row">
						<div class="col-lg-6 mb-lg-3 mb-3">
							<label class="form-label" for="status">Status</label>
							<select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part:module}}/status" data-load-value="id" data-load-label="description" data-default-value="N" required></select>
							<div class="invalid-feedback" data-feedback-input="status"></div>
						</div>

						<div class="col-lg-6 mb-lg-3 mb-3">
							<label class="form-label" for="schoolId">School</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:profile:mainSchoolId}}" required></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-6 mb-lg-3 mb-3">
							<label class="form-label" for="acceptorId">Goed te keuren door</label>
							<select name="acceptorId" id="acceptorId" data-load-source="{{select:url:short}}/users/acceptors" data-load-value="id" data-load-label="fullName" data-search required></select>
							<div class="invalid-feedback" data-feedback-input="acceptorId"></div>
						</div>

						<div class="col-lg-6 mb-lg-3 mb-3">
							<label class="form-label" for="supplierId">Leverancier</label>
							<select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/supplier/overview" data-load-value="id" data-load-label="nameWithMainContact" data-search required></select>
							<div class="invalid-feedback" data-feedback-input="acceptorId"></div>
						</div>
					</div>

					<div class="row">
						<label class="form-label" for="description">Beschrijving</label>
						<input type="text" role="tinymce" name="description" id="description" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="description"></div>
					</div>
				</div>

				<div data-step="2" data-step-title="Bestellijnen">
					<table role="table" id="tbl{{page:id}}Line" data-source="{{table:url:full}}/line" data-small></table>

					<div class="btn-list mt-2 me-auto" id="tbl{{page:id}}LineButtons"></div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="ids" id="ids" />
				<h1>Wenst u deze bestelling(en) te verwijderen?</h1>
			</div>

			<div class="modal-footer" data-form-type="create|update">
				<div role="form-steps-controller"></div>

				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-danger">Ja</button>
			</div>
		</form>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-order-line" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<form action="{{form:url:full}}/line" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}Line" data-action-field="lfaction">
			<input type="hidden" name="orderId" id="orderId" />
			<div class="modal-header">
				<h5 class="modal-title">Bestellijn</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" data-form-type="create|update">
				<div class="row">
					<div class="col-lg-2 mb-lg-3 mb-3">
						<label class="form-label" for="amount">Aantal</label>
						<input type="number" step="1" min="0" name="amount" id="amount" class="form-control" required>
						<div class="invalid-feedback" data-feedback-input="amount"></div>
					</div>

					<div class="col-lg-3 mb-lg-3 mb-3">
						<label class="form-label" for="for">Voor</label>
						<select name="for" id="for" data-load-source="{{select:url:short}}/{{url:part:module}}/line/for" data-load-value="id" data-load-label="description" data-on-change="check" required></select>
						<div class="invalid-feedback" data-feedback-input="for"></div>
					</div>

					<div class="col-lg-7 mb-lg-3 mb-3">
						<label class="form-label" for="assetId">Toestel</label>
						<select name="assetId" id="assetId" data-load-source="[computer@{{select:url:short}}/management/computer;printer@{{select:url:short}}/management/printer;beamer@{{select:url:short}}/management/beamer]" data-load-value="id" data-load-label="[computer@name;printer@name;beamer@shortDescription]" data-search required></select>
						<div class="invalid-feedback" data-feedback-input="assetId"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-6 mb-lg-3 mb-3">
						<label for="what" class="form-label">Wat is er nodig</label>
						<input type="text" name="what" id="what" class="form-control" required />
						<div class="invalid-feedback" data-feedback-input="what"></div>
					</div>

					<div class="col-lg-6 mb-lg-3 mb-3">
						<label for="reason" class="form-label">Reden</label>
						<input type="text" name="reason" id="reason" class="form-control" />
						<div class="invalid-feedback" data-feedback-input="reason"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3 mb-lg-3 mb-3">
						<label for="quotationPrice" class="form-label">Offerte prijs</label>
						<input type="number" step="0.01" min="0" name="quotationPrice" id="quotationPrice" class="form-control" />
						<div class="invalid-feedback" data-feedback-input="quotationPrice"></div>
					</div>

					<div class="col mb-lg-3 mb-3 pt-5">
						<label class="form-check">
							<input type="checkbox" name="quotationVatIncluded" id="quotationVatIncluded" class="form-check-input">
							<span class="form-check-label">btw. inbegrepen</span>
						</label>
						<div class="invalid-feedback" data-feedback-input="quotationVatIncluded"></div>
					</div>

					<div class="col mb-lg-3 mb-3 pt-5">
						<label class="form-check">
							<input type="checkbox" name="warenty" id="warenty" class="form-check-input">
							<span class="form-check-label">Garantie?</span>
						</label>
						<div class="invalid-feedback" data-feedback-input="warenty"></div>
					</div>
				</div>
			</div>

			<div class="modal-body" data-form-type="delete">
				<input type="hidden" name="lids" id="lids" />
				<h1>Wenst u deze bestellijn(en) te verwijderen?</h1>
			</div>

			<div class="modal-footer" data-form-type="create|update">
				<button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-danger">Ja</button>
			</div>
		</form>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-order-request-quote" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal modal-dialog-centered modal-dialog-scrollable" role="document">
		<form action="{{form:url:full}}/request/quote" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}RequestQuote">
			<div class="modal-header">
				<h5 class="modal-title">Offerte aanvragen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" name="rqids" id="rqids" />
				<h1>Wenst u voor deze bestelling(en) een offerte aan te vragen?</h1>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-success">Ja</button>
			</div>
		</form>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-order-request-accept" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal modal-dialog-centered modal-dialog-scrollable" role="document">
		<form action="{{form:url:full}}/request/accept" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}RequestAccept">
			<div class="modal-header">
				<h5 class="modal-title">Goedkeuring aanvragen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" name="raids" id="raids" />
				<h1>Wenst u voor deze bestelling(en) een goedkeuring aan te vragen?</h1>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-success">Ja</button>
			</div>
		</form>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-order-post" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal modal-dialog-centered modal-dialog-scrollable" role="document">
		<form action="{{form:url:full}}/request/post" method="post" class="modal-content" autocomplete="off" id="frm{{page:id}}PostOrder">
			<div class="modal-header">
				<h5 class="modal-title">Bestelling plaatsen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" name="poids" id="poids" />
				<h1>Wenst u voor deze bestelling(en) een bestelling te plaatsen?</h1>
			</div>

			<div class="modal-footer" data-form-type="delete">
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Nee</button>
				<button type="submit" class="btn btn-success">Ja</button>
			</div>
		</form>
	</div>
</div>