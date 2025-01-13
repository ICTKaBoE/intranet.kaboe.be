<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="{{site:url}}">
                <img src="{{site:url}}/frontend/shared/default/images/SGKaBoE blad.png" alt="" class="navbar-brand-image">
                <span class="ms-2">{{setting:site.title.intranet}}</span>
            </a>
        </h1>

        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-link d-flex lh-1 text-reset p-0">
                <span class="avatar avatar-sm me-2">{{user:initials}}</span>
                <a href="/user/logout" class="avatar avatar-sm"><?= \Helpers\HTML::Icon("logout", "Afmelden..."); ?></a>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                {{navbar:items}}
            </ul>
        </div>
    </div>
</aside>