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
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm">{{user:initials}}</span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{user:fullName}}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="/user/profile" class="dropdown-item">Profiel</a>
                    <a href="/user/logout" class="dropdown-item">Afmelden</a>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                {{navbar:items}}
            </ul>
        </div>
    </div>
</aside>