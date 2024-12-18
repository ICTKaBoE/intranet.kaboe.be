<div class="modal modal-blur fade show" id="modal-history" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-full-width modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="delete" autocomplete="off" id="frm{{page:id}}Delete" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uitleengeschiedenis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table role="table" id="tbl{{page:id}}History" data-source="{{table:url:full}}History" data-no-info data-no-paging data-no-search></table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal">Sluiten</button>
            </div>
        </form>
    </div>
</div>