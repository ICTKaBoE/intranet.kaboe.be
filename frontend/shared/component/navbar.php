<?php

use Security\User;
use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Database\Repository\Module;
use Database\Repository\ModuleNavigation;

$module = (new Module)->getByModule(Helpers::getModule());
$navItems = (new ModuleNavigation)->getByModuleId($module->id);

$navItems = Arrays::filter($navItems, function ($ni) use ($module) {
	return User::hasPermissionToEnterSub($ni, $module->id, User::getLoggedInUser()->id);
});
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