<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <div class="navbar-brand navbar-brand-autodark h1">
                <a href="{{site:url}}">
                    <img src="{{site:url}}/frontend/shared/default/images/SGKaBoE blad.png" alt="" class="navbar-brand-image">
                    <span class="ms-2">{{setting:site.title.default}}</span>
                </a>
            </div>
        </div>

        <div class="card card-md">
            <div class="card-body">
                <form action="{{api:url}}/user/login" method="post" autocomplete="off" novalidate="" data-dwl-watching="1">
                    <div class="mb-3">
                        <label class="form-label" for="username">Gebruikersnaam</label>
                        <input type="email" name="username" id="username" class="form-control" placeholder="john.doe@coltd.be" required autofocus>
                        <div class="invalid-feedback" data-feedback-input="username"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="password">Wachtwoord</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Wachtwoord" required>
                        <div class="invalid-feedback" data-feedback-input="password"></div>
                    </div>

                    <div class="form-footer">
                        <button type="submit" id="btn-submit" class="btn btn-primary w-100">Aanmelden</button>
                    </div>
                </form>
            </div>

            <div class="hr-text">of</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <a href="{{o365:connect}}" class="btn btn-secondary w-100">Aanmelden via Office 365</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>