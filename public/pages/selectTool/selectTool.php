<?php

use Security\User;
use Ouzo\Utilities\Strings;
use Database\Repository\Tool;
use Database\Repository\ToolPermission;
use Ouzo\Utilities\Arrays;

$tools = (new Tool)->get();
$user = User::get();
$toolPermissionRepo = new ToolPermission();
$tools = Arrays::filter($tools, fn ($t) => $toolPermissionRepo->hasPermission($t->id, (Strings::equalsIgnoreCase(\Security\User::signInMethod(), 'local') ? $user->username : $user->getMail())));

?>
<div class="row row-deck row-cards">
    <?php foreach ($tools as $tool) : ?>
        <div class="col-sm-6 col-lg-3">
            <a class="card card-sm" href="<?= (new Security\Request)->setParameter(REQUEST_ROUTE_PARAMETER_TOOL, $tool->routeTool)->write(); ?>">
                <div class="card-body">
                    <div class="row align-items-center">
                        <?php if ($tool->icon) : ?>
                            <div class="col-auto">
                                <span class="bg-<?= $tool->iconColor; ?> text-white avatar avatar-lg">
                                    <?= Helpers\Icon::load($tool->icon); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="col">
                            <div class="font-weight-medium markdown">
                                <h1><?= $tool->name; ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>