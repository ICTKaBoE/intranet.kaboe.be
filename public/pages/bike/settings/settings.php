<form action="<?= Core\Page::formAction('POST'); ?>" method="post" class="card" data-prefill="<?= Core\Page::formAction('GET'); ?>">
    <ul class="nav nav-tabs" data-bs-toggle="tabs">
        <li class="nav-item"><a href="#general" class="nav-link show active" data-bs-toggle="tab">Algemeen</a></li>
        <li class="nav-item"><a href="#prices" class="nav-link" data-bs-toggle="tab">Prijzen</a></li>
    </ul>

    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane show active" id="general">
                <div class="row mb-2">
                    <div class="col-lg-2"><label for="lastPayDate" class="form-label">Laatste uitbetalingsdatum</label></div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-4 mb-2">
                                <div class="input-icon">
                                    <input role="datepicker" name="lastPayDate" id="lastPayDate" class="form-control" required />
                                    <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                                </div>
                                <div class="invalid-feedback" data-feedback-input="lastPayDate"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-2"><label for="blockPastRegistration" class="form-label">Blokkeer registraties in het verleden</label></div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-4 mb-2">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="blockPastRegistration" id="blockPastRegistration" required>
                                </label>
                                <div class="invalid-feedback" data-feedback-input="blockPastRegistration"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-2"><label for="blockFutureRegistration" class="form-label">Blokkeer toekomstige registraties</label></div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-4 mb-2">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="blockFutureRegistration" id="blockFutureRegistration" required>
                                </label>
                                <div class="invalid-feedback" data-feedback-input="blockFutureRegistration"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-2"><label for="colorDistance1" class="form-label">Kleur afstand 1</label></div>
                    <div class="col-lg-8">
                        <div class="row g-2">
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="dark" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-dark rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput form-colorinput-light">
                                    <input type="radio" value="white" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-white rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="blue" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-blue rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="azure" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-azure rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="indigo" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-indigo rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="purple" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-purple rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="pink" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-pink rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="red" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-red rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="orange" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-orange rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="yellow" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-yellow rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="green" class="form-colorinput-input" name="colorDistance1" />
                                    <span class="form-colorinput-color bg-green rounded-circle"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-2"><label for="colorDistance2" class="form-label">Kleur afstand 2</label></div>
                    <div class="col-lg-8">
                        <div class="row g-2">
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="dark" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-dark rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput form-colorinput-light">
                                    <input type="radio" value="white" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-white rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="blue" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-blue rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="azure" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-azure rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="indigo" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-indigo rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="purple" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-purple rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="pink" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-pink rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="red" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-red rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="orange" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-orange rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="yellow" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-yellow rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input type="radio" value="green" class="form-colorinput-input" name="colorDistance2" />
                                    <span class="form-colorinput-color bg-green rounded-circle"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="prices">
                <table class="table table-responsive mb-0" id="<?= Core\Page::id('tbl'); ?>" data-wrap="false" data-source="<?= Core\Page::formAction("get", 'prices'); ?>" data-entries="false" data-search="false" data-info="false" data-pagination="false"></table>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="d-flex">
            <button type="submit" role="button" class="ms-auto btn btn-success">Opslaan</button>
        </div>
    </div>
</form>