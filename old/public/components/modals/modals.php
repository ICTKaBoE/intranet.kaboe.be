<div class="modal modal-blur fade show" tabindex="-1" role="dialog" aria-modal="true" id="pageError">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <?= Helpers\Icon::load('alert-triangle', add: ['class' => ['icon-lg', 'text-danger', 'mb-2']]); ?>
                <h1>Foutmelding</h1>
                <div id="pageErrorContent"></div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                OK! <span class="ms-1" id="pageErrorTimer"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade show" tabindex="-1" role="dialog" aria-modal="true" id="pageSuccess">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-green"></div>
            <div class="modal-body text-center py-4">
                <?= Helpers\Icon::load('circle-check', add: ['class' => ['icon-lg', 'text-green', 'mb-2']]); ?>
                <h1>Top!</h1>
                <div id="pageSuccessContent"></div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-success w-100" data-bs-dismiss="modal">
                                OK! <span class="ms-1" id="pageSuccessTimer"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>