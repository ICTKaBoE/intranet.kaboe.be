<?php

use Router\Helpers;
use Database\Repository\Module;
use Database\Repository\ModuleNavigation;

$module = (new Module)->getByModule(Helpers::get_module());
$page = (new ModuleNavigation)->getByModuleAndPage($module->id, Helpers::get_page());
?>

<div class="container-fluid">
	<div class="page-header">
		<div class="row align-items-center">
			<div class="col">
				<h2 class="page-title"><?= $module->name . (is_null($page) ? '' : " - " . $page->name); ?></h2>
			</div>
		</div>
	</div>
</div>