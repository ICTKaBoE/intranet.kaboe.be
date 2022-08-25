<div class="page-center">
    <div class="container-tight py-4 mt-5">
        <div class="text-center mb-4">
            <a href="." class="navbar-brand navbar-brand-autodark">
                <!-- <img src="./static/logo.svg" height="36" alt=""> -->
                <?= Core\Config::get("site/title"); ?>
            </a>
        </div>
        <form class="card card-md" action="<?= Core\Page::formAction('post'); ?>" method="post" autocomplete="off" id="<?= Core\Page::id('frm'); ?>">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Aanmelden met je COLTD-adres</h2>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@coltd.be" required>
                    <div class="invalid-feedback" data-feedback-input="email"></div>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Wachtwoord" required>
                    <!-- <span class="input-group-text">
                            <a href="#" class="link-secondary" title="" data-bs-toggle="tooltip" data-bs-original-title="Show password">
                                Download SVG icon from http://tabler-icons.io/i/eye
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="12" cy="12" r="2"></circle>
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                                </svg>
                            </a>
                        </span> -->
                    <div class="invalid-feedback" data-feedback-input="password"></div>
                </div>
                <div class="mb-2">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input">
                        <span class="form-check-label">Onthou me in deze browser</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <?= Helpers\Icon::load("login"); ?>Aanmelden
                    </button>
                </div>
            </div>
            <div class="hr-text">of</div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <a href="<?= Security\Request::host(); ?>/public/scripts/o365/login.php" class="btn btn-white w-100">
                            <?= Helpers\Icon::load("cloud"); ?>
                            Aanmelden via Office 365
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>