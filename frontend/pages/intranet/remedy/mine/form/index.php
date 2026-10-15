<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row">
    <div class="col-lg-4 mx-auto col-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="schoolId" class="form-label">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-default-value="{{user:mainSchoolId}}"></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="departmentId">Afdeling</label>
                        <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-parent-select="schoolId" data-on-change="departmentView" required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="typeId" class="form-label">Type</label>
                        <select name="typeId" id="typeId" data-load-source="{{select:url:short}}/{{url:part.module}}/typeAdvanced" data-parent-select="departmentId" data-on-change="typeView" data-extra="[show=limit]" data-default-no-load required></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="courseId" class="form-label">Vak</label>
                        <select name="courseId" id="courseId" data-load-source="{{select:url:short}}/{{url:part.module}}/course" data-parent-select="typeId" data-on-change="courseView" data-search data-default-no-load required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="momentId" class="form-label">Tijdstip</label>
                        <select name="momentId" id="momentId" data-load-source="{{select:url:short}}/{{url:part.module}}/moment" data-label="formatted.shortDescription" data-default-no-load required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="informatStudentId" class="form-label">Leerling</label>
                        <input type="hidden" name="informatClassgroupId" id="informatClassgroupId" />
                        <select name="informatStudentId" id="informatStudentId" data-load-source="{{select:url:short}}/informat/studentPerClassByDepartment" data-label="formatted.fullNameReversed" data-on-change="studentView" data-render-item="renderOptgroupItem" data-default-no-load data-search multiple required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3 d-none">
                        <label for="coursePerStudent" class="form-label">Vak</label>
                        <select name="coursePerStudent" id="coursePerStudent" data-load-source="{{select:url:short}}/{{url:part.module}}/coursePerStudent" data-search data-default-no-load></select>
                    </div>
                </div>

                <div class="row d-none" id="onComputer-Y">
                    <div class="col-12 mb-3">
                        <label for="computerType" class="form-label">Opdracht te vinden op (indien op computer)</label>
                        <select name="computerType" id="computerType" data-load-source="{{select:url:short}}/{{url:part.module}}/computerType" data-on-change="computerTypeView"></select>
                    </div>

                    <div class="col-12 mb-3 d-none" id="computerType-O">
                        <label for="computerTypeOther" class="form-label">Opdracht te vinden op - Andere</label>
                        <input type="text" name="computerTypeOther" id="computerTypeOther" class="form-control" required />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="computerPassword" class="form-label">Wachtwoord</label>
                        <input type="text" name="computerPassword" id="computerPassword" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="remark" class="form-label">Opmerking</label>
                        <textarea name="remark" id="remark" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="button" id="btnSave"></button>
                <button type="button" id="btnCancel"></button>
            </div>
        </div>
    </div>
</form>