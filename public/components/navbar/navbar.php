<?php

use Security\Request;
use Ouzo\Utilities\Strings;
use Database\Repository\Tool;
use Database\Repository\Navigation;
use Database\Repository\ToolPermission;
use Ouzo\Utilities\Arrays;
use Security\Session;

$rightsOrder = [
    1 => 'read',
    2 => 'write',
    3 => 'react',
    4 => 'export',
    5 => 'changeSettings'
];

$tool = (new Tool)->getByRoute(Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL));
$navItems = (new Navigation)->getByToolId($tool->id);
$currentPage = Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) ? Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) : Core\Config::get("page/default/tool");
$toolPermissions = (new ToolPermission)->getByUpn(Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);

$navItems = Arrays::filter($navItems, function ($ni) use ($tool, $toolPermissions, $rightsOrder) {
    if ($toolPermissions[0]->toolId === 0) return true;
    else {
        $toolPermission = Arrays::firstOrNull(Arrays::filter($toolPermissions, fn ($tp) => $tool->id === $tp->toolId));

        if (!is_null($toolPermission)) {
            $hasPermission = false;

            foreach ($rightsOrder as $index => $value) {
                if (Strings::equalsIgnoreCase($ni->minimumRights, $value)) {
                    if ($toolPermission->toArray()[$value] === 1) {
                        $hasPermission = true;
                        break;
                    }
                }
            }

            return $hasPermission;
        } else return false;
    }
});
?>

<?php if (count($navItems)) : ?>
    <div class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar navbar-light">
                <div class="container-fluid">
                    <ul class="navbar-nav">
                        <?php foreach ($navItems as $navItem) : ?>
                            <li class="nav-item <?= (Strings::equal($currentPage, $navItem->routePage) ? 'active' : ''); ?>">
                                <a class="nav-link" href="<?= (new Security\Request)->setParameter(REQUEST_ROUTE_PARAMETER_PAGE, $navItem->routePage)->removeParamter(REQUEST_ROUTE_PARAMETER_ID)->write(); ?>">
                                    <?php if ($navItem->icon) : ?>
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <?= \Helpers\Icon::load($navItem->icon); ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="nav-link-title"><?= $navItem->name; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>