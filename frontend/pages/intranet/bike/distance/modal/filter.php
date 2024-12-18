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
                        <label class="form-label" for="type">Type</label>
                        <select name="type" id="type" data-load-source="{{select:url:full}}Type" data-load-value="id" data-load-label="name" data-on-change="changeLocationType" data-default-value="HW" required></select>
                        <div class="invalid-feedback" data-feedback-input="type"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="startId">Startlocatie</label>
                        <select name="startId" id="startId" data-load-source="[HW@{{select:url:short}}/user/address;WW@{{select:url:short}}/school/address]" data-load-value="id" data-load-label="[HW@address;WW@addressWithSchool]" data-default-details="HW" required></select>
                        <div class="invalid-feedback" data-feedback-input="startId"></div>
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