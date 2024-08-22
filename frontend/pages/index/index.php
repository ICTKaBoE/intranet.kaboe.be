<div class="row h-100 m-0">
    <div class="col d-none d-md-block"></div>

    <div class="card rounded-0 col-12 col-md-6 col-lg-3">
        <div class="container-tight py-4 mt-5">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark h1">{{site.title}}</a>
                <h3 class="card-title">Aanmelden met je COLTD-adres</h3>
            </div>

            <form action="{{api:url}}/user/login" method="post" autocomplete="off" id="frm{{page:id}}">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="username">Email</label>
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
                </div>
            </form>

            <div class="hr-text">of</div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <a href="{{o365:connect}}" class="btn btn-secondary w-100">Aanmelden via Office 365</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="position-absolute bottom-0 end-0 p-3">
            Versie: {{site.version}}
        </div>
    </div>
</div>