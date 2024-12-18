<div class="modal modal-blur fade show" id="modal-battery" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batterijstatus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table role="table" id="tbl{{page:id}}Battery" data-source="{{table:url:full}}Battery" data-no-search data-no-info data-no-paging></table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Sluiten</button>
            </div>
        </div>
    </div>
</div>