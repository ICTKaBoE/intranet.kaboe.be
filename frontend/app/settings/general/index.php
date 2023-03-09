<div class="row row-cards">
	<div class="col-12">
		<form action="{{form:action}}" method="post" id="frm{{page:id}}" class="card" data-prefill="{{form:action}}">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">{{settings:general:navitems}}</ul>
			</div>

			<div class="card-body">
				<div class="tab-content">{{settings:general:tabs}}</div>
			</div>

			<div class="card-footer text-end">
				<button type="submit" class="btn btn-primary">Opslaan</button>
			</div>
		</form>
	</div>
</div>