<div class="modal modal-blur fade show" id="modal-filter" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filteren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label mb-1" for="status">Status</label>
                        <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name" data-default-value="WA;A;D;S;R;PR" multiple></select>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-primary" onclick="emptyFilter()">Filter Legen</button>
                <button type="button" class="btn btn-primary" onclick="filter()">Filter Toepassen</button>
            </div>
        </div>
    </div>
</div>