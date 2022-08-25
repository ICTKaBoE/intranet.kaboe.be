<form action="<?= Core\Page::formAction('POST'); ?>" method="post" class="card" data-prefill="<?= Core\Page::formAction('GET'); ?>">
    <div class="card-body">
        <fieldset class="form-fieldset">
            <legend>Site</legend>
            <div class="row">
                <div class="col-lg-2"><label for="site.title" class="form-label">Titel</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="site.title" id="site.title" required>
                            <div class="invalid-feedback" data-feedback-input="site.title"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="site.version" class="form-label">Versie</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="site.version" id="site.version" required>
                            <div class="invalid-feedback" data-feedback-input="site.version"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="site.language" class="form-label">Taal</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="site.language" id="site.language" required>
                            <div class="invalid-feedback" data-feedback-input="site.language"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="site.direction" class="form-label">Richting</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="site.direction" id="site.direction" required>
                            <div class="invalid-feedback" data-feedback-input="site.direction"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend>Pagina</legend>
            <div class="row">
                <div class="col-lg-2"><label for="page.login" class="form-label">Login</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="page.login" id="page.login" required>
                            <div class="invalid-feedback" data-feedback-input="page.login"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="page.default.afterLogin" class="form-label">Na aanmelding</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="page.default.afterLogin" id="page.default.afterLogin" required>
                            <div class="invalid-feedback" data-feedback-input="page.default.afterLogin"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="page.default.tool" class="form-label">Na tool select</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="page.default.tool" id="page.default.tool" required>
                            <div class="invalid-feedback" data-feedback-input="page.default.tool"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="card-footer">
        <div class="d-flex">
            <button type="submit" role="button" class="ms-auto btn btn-success">Opslaan</button>
        </div>
    </div>
</form>