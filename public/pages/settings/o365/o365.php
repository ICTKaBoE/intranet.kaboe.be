<form action="<?= Core\Page::formAction('POST'); ?>" method="post" class="card" data-prefill="<?= Core\Page::formAction('GET'); ?>">
    <div class="card-body">
        <fieldset class="form-fieldset">
            <legend>Algemeen</legend>
            <div class="row">
                <div class="col-lg-2"><label for="o365.enabled" class="form-label">Aan/Uit</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.enabled" id="o365.enabled" required>
                            <div class="invalid-feedback" data-feedback-input="o365.enabled"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.tenant" class="form-label">Tenant</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.tenant" id="o365.tenant" required>
                            <div class="invalid-feedback" data-feedback-input="o365.tenant"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend>Client</legend>
            <div class="row">
                <div class="col-lg-2"><label for="o365.client.id" class="form-label">Id</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.client.id" id="o365.client.id" required>
                            <div class="invalid-feedback" data-feedback-input="o365.client.id"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.client.secret" class="form-label">Secret</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.client.secret" id="o365.client.secret" required>
                            <div class="invalid-feedback" data-feedback-input="o365.client.secret"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend>URL</legend>
            <div class="row">
                <div class="col-lg-2"><label for="o365.url.authority" class="form-label">Authority</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.url.authority" id="o365.url.authority" required>
                            <div class="invalid-feedback" data-feedback-input="o365.url.authority"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.url.callback" class="form-label">Callback</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.url.callback" id="o365.url.callback" required>
                            <div class="invalid-feedback" data-feedback-input="o365.url.callback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.url.resource" class="form-label">Resource</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.url.resource" id="o365.url.resource" required>
                            <div class="invalid-feedback" data-feedback-input="o365.url.resource"></div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend>Endpoint</legend>
            <div class="row">
                <div class="col-lg-2"><label for="o365.endpoint.authorize" class="form-label">Authorize</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.endpoint.authorize" id="o365.endpoint.authorize" required>
                            <div class="invalid-feedback" data-feedback-input="o365.endpoint.authorize"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.endpoint.token" class="form-label">Token</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.endpoint.token" id="o365.endpoint.token" required>
                            <div class="invalid-feedback" data-feedback-input="o365.endpoint.token"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2"><label for="o365.endpoint.logout" class="form-label">Logout</label></div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <input class="form-control" type="text" name="o365.endpoint.logout" id="o365.endpoint.logout" required>
                            <div class="invalid-feedback" data-feedback-input="o365.endpoint.logout"></div>
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