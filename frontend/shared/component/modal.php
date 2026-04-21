<!-- Default Spinner Modals -->
<div class="modal modal-blur fade" id="modal-wait" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered d-flex justify-content-center" role="document">
		<div class="spinner-border spinner-border-xl text-bold" style="width: 5rem; height: 5rem;" role="status" role="status"></div>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-wait-print" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered d-flex justify-content-center" role="document">
		<div class="spinner-border spinner-border-xl text-bold" style="width: 5rem; height: 5rem;" role="status" role="status"></div>
		<span class="ms-5 fs-3">Uw documenten worden gegenereerd.<br />Gelieve even te wachten!</span>
	</div>
</div>

<div class="modal modal-blur fade" id="modal-wait-delete" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered d-flex justify-content-center" role="document">
		<div class="spinner-border spinner-border-xl text-bold" style="width: 5rem; height: 5rem;" role="status" role="status"></div>
		<span class="ms-5 fs-3">Uw items worden verwijderd.<br />Gelieve even te wachten!</span>
	</div>
</div>

<!-- Default Print Modal -->
<div class="modal modal-blur fade" id="modal-print" tabindex="-1" role="dialog" aria-modal="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="{{form:url:full}}Print" method="post" autocomplete="off" id="frm{{page:id}}Print" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Printen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" name="_method" value="PRINT" />
				<input type="hidden" name="ids" id="ids" />
				<h1>Bent u zeker dat u de geselecteerde items wilt printen?</h1>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-success">Ja</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nee</button>
			</div>
		</form>
	</div>
</div>

<!-- Default Delete Modal -->
<div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-modal="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}Delete" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Verwijderen</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" name="_method" value="DELETE" />
				<input type="hidden" name="ids" id="ids" />
				<h1>Bent u zeker dat u de geselecteerde items wilt verwijderen?</h1>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-danger">Ja</button>
				<button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
			</div>
		</form>
	</div>
</div>

<!-- Default Filter Modal -->
<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" role="dialog" aria-modal="true" data-module="{{url:part.module}}" data-page="{{url:part.page}}">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filteren</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">{{filter:fields}}</div>

			<div class="modal-footer">
				<button type="button" class="btn me-auto" data-bs-dismiss="modal">Sluiten</button>
				<button type="button" class="btn btn-primary" onclick="emptyFilter()">Filter Legen</button>
				<button type="button" class="btn btn-primary" onclick="filter()">Filter Toepassen</button>
			</div>
		</div>
	</div>
</div>