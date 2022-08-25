<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-1">
                    <div class="col-1">
                        <label for="<?= Core\Page::id('sel'); ?>" class="form-label col-form-label">Kies leerkracht</label>
                    </div>

                    <div class="col-4">
                        <select name="upn" id="<?= Core\Page::id('sel'); ?>" class="form-select" data-on-change="upnOnChange" data-load-value="mail" data-load-label="displayName" data-load-source="<?= Core\Page::formAction("get", 'upn'); ?>"></select>
                    </div>

                    <div class="col">
                        <button class="btn btn-success" onclick="search()">Zoeken</button>
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-1">
                        <label for="start" class="form-label col-form-label">Startdatum</label>
                    </div>

                    <div class="col-4">
                        <div class="input-icon">
                            <input role="datepicker" name="start" id="start" class="form-control" required />
                            <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-1">
                        <label for="end" class="form-label col-form-label">Einddatum</label>
                    </div>

                    <div class="col-4">
                        <div class="input-icon">
                            <input role="datepicker" name="end" id="end" class="form-control" required />
                            <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-responsive mb-0" id="<?= Core\Page::id('tbl'); ?>" data-wrap="false" data-source="<?= Core\Page::formAction("get"); ?>" data-entries="false" data-search="false" data-info="false" data-pagination="false"></table>
            </div>
        </div>
    </div>
</div>

<script>
    let selectId = "<?= Core\Page::id('sel'); ?>";
    let tableId = "<?= Core\Page::id('tbl'); ?>";
</script>