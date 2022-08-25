<?php

error_reporting(E_ALL);
require_once __DIR__ . "/app/autoload.php";
Core\Bootstrap::init();
?>

<!DOCTYPE html>
<html dir="<?= Core\Config::get("site/direction"); ?>" lang="<?= Core\Config::get('site/language'); ?>">

<head>
    <title><?= Core\Config::get("site/title"); ?></title>

    <?= Core\Document::load(); ?>
    <?= Core\Page::stylesheet(); ?>
</head>

<body class="theme-light">
    <div class="page">
        <?= Core\Page::header(); ?>
        <?= Core\Page::navbar(); ?>
        <div class="page-wrapper">
            <?= Core\Page::pagetitle(); ?>
            <div class="page-body">
                <div class="container-fluid">
                    <?= Core\Page::content(); ?>
                </div>
            </div>
            <?= Core\Page::footer(); ?>
        </div>
    </div>

    <?= Core\Page::modals(); ?>

    <?= Core\Page::javascript(); ?>
    <?= Core\Document::load("body"); ?>
</body>

</html>