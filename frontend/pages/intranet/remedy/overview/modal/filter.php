<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filteren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label mb-1" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-default-value="{{user:mainSchoolId}}"></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label mb-1" for="departmentId">Afdeling</label>
                        <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-parent-select="schoolId" data-disable-if-no-options data-default-no-load></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label mb-1" for="typeId">Type</label>
                        <select name="typeId" id="typeId" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-parent-select="departmentId" data-disable-if-no-options data-default-no-load></select>
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