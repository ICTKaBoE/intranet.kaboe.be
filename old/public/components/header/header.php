<?php $user = Security\User::get(); ?>

<header class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href=".">
                <!-- <img src="./static/logo.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image"> -->
                <?= Core\Config::get("site/title"); ?>
            </a>
        </h1>

        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm"><?= Helpers\Strings::firstLetterOfWords($user->getDisplayName()); ?></span>
                    <div class="d-none d-xl-block ps-2">
                        <div><?= $user->getDisplayName(); ?></div>
                        <div class="mt-1 small text-muted"><?= $user->getJobTitle() ?> (<?= $user->getCompanyName(); ?>)</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <?php if (Security\Session::get(SECURITY_SESSION_ISSIGNEDIN)['method'] == 'o365') : ?>
                        <a href="<?= Security\Request::host(); ?>/public/scripts/o365/logout.php" class="dropdown-item">Afmelden</a>
                    <?php else : ?>
                        <a href="<?= Security\Request::host(); ?>/public/scripts/security/logout.php" class="dropdown-item">Afmelden</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>