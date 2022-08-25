<?php

use Database\Repository\Navigation;
use Security\Request;
use Database\Repository\Tool;
use Security\Session;

$tool = (new Tool)->getByRoute(Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL));
$page = (new Navigation)->getByToolAndRoute($tool->id, Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) ? Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) : Core\Config::get("page/default/tool"));

$pagetitle = $tool->name . (empty($page) ? "" : " - {$page->name}");
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title"><?= $pagetitle; ?></h2>
            </div>
        </div>
    </div>
</div>