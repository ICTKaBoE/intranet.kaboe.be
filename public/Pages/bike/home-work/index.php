<?php

use Database\Repository\UserHomeWorkDistance;
use Ouzo\Utilities\Arrays;
use Security\User;

$distances = Arrays::orderBy((new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id), 'id');
?>

<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card mb-3">
			<div class="card-header">
				<h4 class="card-title">Mededeling</h4>
			</div>

			<div class="card-body">
				<p>
					Bij wijziging van je profiel, moeten alle fietsdagen van vóór de wijziging ingevoerd zijn.<br />
					Pas daarna pas je je profiel aan.
				</p>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Legenda</h4>
			</div>

			<div class="card-body">
				<?php foreach ($distances as $distance) : ?>
					<div class="row mb-1">
						<div class="col-2" style="background-color: <?= $distance->color; ?>"></div>
						<div class="col"><?= $distance->alias; ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-2 bg-blue"></div>
					<div class="col">Feestdag / Schoolvakantie</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6 mb-3">
		<div class="card">
			<div class="card-body">
				<div id="cal{{id}}" role="calendar" data-source="[{{calendar:action:get}},{{site:url}}/app/scripts/GET/Calendar/holliday.php]" data-action="{{form:action:post}}" data-date-click="setRide"></div>
			</div>
		</div>
	</div>

	<div class="col-md-3">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Handleiding</h3>
			</div>
			<div class="card-body">
				<p>Klik op de dag waarop je met de fiets kwam.</p>
				<p>Je kan zoveel klikken als je wil, de kleur die overeenkomt met jou ingestelde rit, is de rit die opgeslagen wordt.</p>
				<p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
			</div>
		</div>
	</div>
</div>

<script>
	let calendarId = "cal{{id}}";
</script>