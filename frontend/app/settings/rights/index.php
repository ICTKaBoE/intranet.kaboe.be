<div class="row row-cards">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<label for="sel{{page:id}}" class="form-label">Kies de module</label>
				<select name="sel{{page:id}}" id="sel{{page:id}}" data-load-source="{{select:action}}/modulesAssignRights" data-load-value="id" data-load-label="name" data-on-change="loadTable" required></select>
			</div>

			<div class="card-body p-0">
				<table role="table" id="tbl{{page:id}}" data-source="{{table:action}}"></table>
			</div>
		</div>
	</div>
</div>