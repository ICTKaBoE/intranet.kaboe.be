<?php

use Router\Helpers;
use Database\Repository\Module;
use Database\Repository\ModuleNavigation;

$module = (new Module)->getByModule(Helpers::get_module());
$navItems = (new ModuleNavigation)->getByModuleId($module->id);
?>

<?php if (count($navItems)) : ?>
	<div class="navbar-expand-md">
		<div class="collapse navbar-collapse" id="navbar-menu">
			<div class="navbar navbar-light">
				<div class="container-fluid">
					<ul class="navbar-nav">
						<?php foreach ($navItems as $navItem) : ?>
							<li class="nav-item <?= $navItem->isActive ? 'active' : ''; ?>">
								<a class="nav-link" href="<?= $navItem->link; ?>">
									<?php if ($navItem->iconData) : ?>
										<span class="nav-link-icon d-md-none d-lg-inline-block">
											<?= $navItem->iconData; ?>
										</span>
									<?php endif; ?>
									<span class="nav-link-title">
										<?= $navItem->name; ?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>