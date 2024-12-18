<header class="navbar navbar-expand-md navbar-overlap d-print-none" data-bs-theme="dark">
    <div class="container-fluid">
        <?php if ($_COOKIE["component_navigation_mode"] !== "extranet"): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <?php endif; ?>

        <h1 class="navbar-brand navbar-brand-autodark">
            <a href=".">
                <img src="{{site:url}}/frontend/shared/default/images/SGKaBoE blad.png" alt="" class="navbar-brand-image">
                <span class="ms-2"><?php if ($_COOKIE["component_navigation_mode"] == "extranet"): ?>{{setting:site.title.extranet}}<?php else: ?>{{setting:site.title.default}}<?php endif; ?></span>
            </a>
        </h1>

        <?php if ($_COOKIE["component_navigation_mode"] !== "extranet"): ?>
            <div class="collapse navbar-collapse end-0" id="navbar-menu">
                <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                    <ul class="navbar-nav">
                        {{navbar:items}}
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>