<?php

use Router\Helpers;
use Database\Repository\Module;
use Database\Repository\ModuleNavigation;

$module = (new Module)->getByModule(Helpers::getModule());
$page = (new ModuleNavigation)->getByModuleAndPage($module->id, Helpers::getPage());
?>

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-center">
			<div class="col">
				<h2 class="page-title"><?= $module->name . (is_null($page) ? '' : " - " . $page->name); ?></h2>
			</div>

			<div class="col-12 col-md-auto ms-auto">
				<div class="btn-list" id="pagetitle-buttons"></div>
			</div>
		</div>
	</div>
</div>