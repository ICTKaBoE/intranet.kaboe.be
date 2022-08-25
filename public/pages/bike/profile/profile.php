<form action="<?= Core\Page::formAction('POST'); ?>" method="post" class="card" data-prefill="<?= Core\Page::formAction('GET'); ?>">
    <input type="hidden" name="id" id="id" />
    <input type="hidden" name="upn" id="upn" />
    <input type="hidden" name="deleted" id="deleted" />

    <div class="card-body">
        <div class="row">
            <div class="col-lg-2"><label class="form-label">Adres</label></div>
            <div class="col-lg-8">
                <div class="row mb-3">
                    <div class="col-lg-8 mb-2">
                        <label class="form-label" for="address_street">Straat</label>
                        <input class="form-control" type="text" name="address_street" id="address_street" required>
                        <div class="invalid-feedback" data-feedback-input="address_street"></div>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <label class="form-label" for="address_number">Nummer</label>
                        <input class="form-control" type="text" name="address_number" id="address_number" required>
                        <div class="invalid-feedback" data-feedback-input="address_number"></div>
                    </div>

                    <div class="col-lg-2 mb-2">
                        <label class="form-label" for="address_bus">Bus</label>
                        <input class="form-control" type="text" name="address_bus" id="address_bus">
                        <div class="invalid-feedback" data-feedback-input="address_bus"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2 mb-2">
                        <label class="form-label" for="address_zipcode">Postcode</label>
                        <input class="form-control" type="text" name="address_zipcode" id="address_zipcode" required>
                        <div class="invalid-feedback" data-feedback-input="address_zipcode"></div>
                    </div>

                    <div class="col-lg-7 mb-2">
                        <label class="form-label" for="address_city">Stad</label>
                        <input class="form-control" type="text" name="address_city" id="address_city" required>
                        <div class="invalid-feedback" data-feedback-input="address_city"></div>
                    </div>

                    <div class="col-lg-3 mb-2">
                        <label class="form-label" for="address_country">Land</label>
                        <select class="form-select" name="address_country" id="address_country" required data-on-change="addressCountryOnChange">
                            <option value="BE">België</option>
                            <option value="NL">Nederland</option>
                        </select>
                        <div class="invalid-feedback" data-feedback-input="address_country"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-2"><label class="form-label" for="mainSchool">School</label></div>
            <div class="col-lg-4">
                <select class="form-select" name="mainSchool" id="mainSchool" required>
                    <?php foreach (Core\Config::get("schools") as $idx => $school) : ?>
                        <option value="<?= $idx; ?>"><?= $school; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback" data-feedback-input="mainSchool"></div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-2"><label class="form-label" for="distance1">Afstand woon-werk 1<br />Enkele rit</label></div>
            <div class="col-lg-4">
                <div class="input-group">
                    <input type="number" step="0.1" min="0" name="distance1" id="distance1" class="form-control" required />
                    <span class="input-group-text">km</span>
                    <div class="invalid-feedback" data-feedback-input="distance1"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-2"><label class="form-label" for="distance2">Afstand woon-werk 2<br />Enkele rit</label></div>
            <div class="col-lg-4">
                <div class="input-group">
                    <input type="number" step="0.1" min="0" name="distance2" id="distance2" class="form-control" />
                    <span class="input-group-text">km</span>
                    <div class="invalid-feedback" data-feedback-input="distance2"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-2"><label class="form-label" for="bankAccount">IBAN Rekeningnummer</label></div>
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-text" id="bankAccountPrefix">BE</span>
                    <input type="text" name="bankAccount" id="bankAccount" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="bankAccount"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="d-flex">
            <button type="submit" role="button" class="ms-auto btn btn-success">Opslaan</button>
        </div>
    </div>
</form>