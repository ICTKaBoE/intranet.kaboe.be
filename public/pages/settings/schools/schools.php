<div class="row mb-3">
    <div class="col d-flex">
        <div class="ms-auto">
            <button type="button" class="btn btn-danger" onclick="deleteSchools()">
                <?= Helpers\Icon::load("trash"); ?>
                Verwijderen
            </button>

            <button type="button" class="btn btn-warning" onclick="editSchools()">
                <?= Helpers\Icon::load("pencil"); ?>
                Bewerken
            </button>

            <button type="button" class="btn btn-success" onclick="addSchools()">
                <?= Helpers\Icon::load("plus"); ?>
                Toevoegen
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-responsive mb-0" id="<?= Core\Page::id('tbl'); ?>" data-wrap="false" data-source="<?= Core\Page::formAction("get"); ?>" data-entries="false" data-search="false" data-info="false" data-pagination="false"></table>
            </div>
        </div>
    </div>
</div>

<form class="modal modal-blur fade show" tabindex="-1" role="dialog" aria-modal="true" id="<?= Core\Page::id('modal', 'add'); ?>" method="POST" action="<?= Core\Page::formAction('POST', 'add'); ?>">
    <input type="hidden" name="modalId" value="<?= Core\Page::id('modal', 'add'); ?>">
    <input type="hidden" name="tableId" value="<?= Core\Page::id('tbl'); ?>">

    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <?= Helpers\Icon::load('plus', add: ['class' => ['icon-lg', 'text-green', 'mb-2']]); ?>
                <div id="<?= Core\Page::id('modal', 'add'); ?>Content">
                    <label for="name" class="form-label">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Annuleer</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-success w-100">Opslaan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    modalAddId = '<?= Core\Page::id('modal', 'add'); ?>';
    tableId = '<?= Core\Page::id('tbl'); ?>';
</script>