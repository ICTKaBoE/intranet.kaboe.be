<div class="row row-cards">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-header">
				<h3>Overzicht tickets per status</h3>
			</div>
			<div class="card-body">
				<div role="chart" data-type="bar" id="crt_status{{page:id}}" data-source="{{chart:url:short}}/helpdesk/dashboard/status"></div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card">
			<div class="card-header">
				<h3>Overzicht tickets per prioriteit (enkel tickets van het tablad "Open")</h3>
			</div>
			<div class="card-body">
				<div role="chart" data-type="bar" id="crt_priority{{page:id}}" data-source="{{chart:url:short}}/helpdesk/dashboard/priority"></div>
			</div>
		</div>
	</div>
</div>