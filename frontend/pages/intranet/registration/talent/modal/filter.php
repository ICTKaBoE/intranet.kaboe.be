<div class="modal modal-blur fade" id="modal-filter" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filteren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="schoolyearId">Schooljaar</label>
                        <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/schoolyear" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name"></select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="studyyearId">Leerjaar</label>
                        <select name="studyyearId" id="studyyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/studyyear" data-load-value="id" data-load-label="name" data-parent-select="schoolId"></select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="fieldId">Studierichting</label>
                        <select name="fieldId" id="fieldId" data-load-source="{{select:url:short}}/{{url:part.module}}/field" data-load-value="id" data-load-label="name" data-parent-select="studyyearId"></select>
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