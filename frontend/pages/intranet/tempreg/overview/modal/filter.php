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
                        <label class="form-label" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>

                    </div>
                </div>

                <!-- <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="start">Periode - Start</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                            <input role="datepicker" name="start" id="start" class="form-control" required />
                        </div>
                        
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="end">Periode - Einde</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                            <input role="datepicker" name="end" id="end" class="form-control" required />
                        </div>
                        
                    </div>
                </div> -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-primary" onclick="emptyFilter()">Filter Legen</button>
                <button type="button" class="btn btn-primary" onclick="filter()">Filter Toepassen</button>
            </div>
        </div>
    </div>
</div>