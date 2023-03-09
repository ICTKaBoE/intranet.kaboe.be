<?php

use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\UserHomeWorkDistance;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Router\Helpers;
use Security\User;

$moduleSettingRepo = new ModuleSetting;
$moduleId = (new Module)->getByModule(Helpers::getModule())->id;

$distances = Arrays::orderBy((new UserHomeWorkDistance)->getByUserId(User::getLoggedInUser()->id), 'id');
$lastPayDate = $moduleSettingRepo->getByModuleAndKey($moduleId, "lastPayDate")->value;
$blockFuture = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFuture")->value == "on";
$blockFutureAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureAmount")->value;
$blockFutureType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockFutureType")->value;
$blockPast = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPast")->value == "on";
$blockPastAmount = (int)$moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastAmount")->value;
$blockPastType = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastType")->value;
$blockPastOnLastPayDate = $moduleSettingRepo->getByModuleAndKey($moduleId, "blockPastOnLastPayDate")->value == "on";

$blockFutureDate = Clock::now();
if ($blockFutureType == "d") $blockFutureDate = $blockFutureDate->plusDays($blockFutureAmount);
else if ($blockFutureType == "w") $blockFutureDate = $blockFutureDate->plusDays($blockFutureAmount * 7);
else if ($blockFutureType == "m") $blockFutureDate = $blockFutureDate->plusMonths($blockFutureAmount);
else if ($blockFutureType == "y") $blockFutureDate = $blockFutureDate->plusYears($blockFutureAmount);

$blockPastDate = Clock::now();
if ($blockPastType == "d") $blockPastDate = $blockPastDate->minusDays($blockPastAmount);
else if ($blockPastType == "w") $blockPastDate = $blockPastDate->minusDays($blockPastAmount * 7);
else if ($blockPastType == "m") $blockPastDate = $blockPastDate->minusMonths($blockPastAmount);
else if ($blockPastType == "y") $blockPastDate = $blockPastDate->minusYears($blockPastAmount);

if ($blockPastOnLastPayDate) {
	if (Clock::at($lastPayDate)->isAfterOrEqualTo($blockPastDate)) $blockPastDate = Clock::at($lastPayDate)->plusDays(1);
}
?>

<div class="row">
	<div class="col-md-3 mb-3">
		<div class="card mb-3">
			<div class="card-header">
				<h4 class="card-title">Mededeling</h4>
			</div>

			<div class="card-body">
				<p>
					Bij wijziging van afstanden, moeten alle fietsdagen van vóór de wijziging ingevoerd zijn.<br />
					Pas daarna pas je je afstanden aan.
				</p>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Legenda</h4>
			</div>

			<div class="card-body">
				<?php foreach ($distances as $distance) : ?>
					<div class="row mb-1 bg-<?= $distance->color; ?>">
						<div class="text-<?= $distance->textColor; ?>"><?= $distance->alias; ?> <i>(<?= number_format($distance->distance, 2, ",", "."); ?> km)</i></div>
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
				<div id="cal{{page:id}}" role="calendar" data-source="[{{calendar:action}},{{api:url}}/calendar/holliday]" data-action="{{form:action}}" data-date-click="setRide" <?php if ($blockPast) : ?>data-range-start="<?= $blockPastDate->format("Y-m-d"); ?>" <?php endif; ?> <?php if ($blockFuture) : ?>data-range-end="<?= $blockFutureDate->format("Y-m-d"); ?>" <?php endif; ?>></div>
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
				<p>
					Je kan zoveel klikken als je wil, de kleur die overeenkomt met jou ingestelde rit, is de rit die opgeslagen wordt.<br />
					Zie je geen kleur meer, dan heb je die dag uitgeschakeld.
				</p>
				<p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
			</div>
		</div>
	</div>
</div>

<script>
	let calendarId = "cal{{page:id}}";
</script>