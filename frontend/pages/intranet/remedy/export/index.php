<div class="card col-12 col-lg-6 mx-auto">
    <form class="card" action="{{form:url:full}}" method="POST" id="frm{{page:id}}">
        <input type="hidden" name="_method" value="PRINT" />
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" required></select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="departmentId">Afdeling</label>
                    <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-parent-select="schoolId" data-disable-if-no-options data-default-no-load></select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="typeId">Type</label>
                    <select name="typeId" id="typeId" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-parent-select="departmentId" data-disable-if-no-options data-default-no-load multiple></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label for="start" class="form-label">Start datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="start" id="start" class="form-control" required />
                    </div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="end" class="form-label">Eind datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="end" id="end" class="form-control" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Exporteren</button>
        </div>
    </form>
</div>