<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row">
    <input type="hidden" name="postData" id="postData" />
    <div class="col-lg-4 col-12 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-12 mb-3">
                        <label class="form-label" for="departmentId">Afdeling</label>
                        <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-on-change="departmentView" required></select>
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label for="typeId" class="form-label">Type</label>
                        <select name="typeId" id="typeId" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-parent-select="departmentId" data-on-change="typeView" data-extra="[show=limit]" data-disable-if-no-options data-default-no-load required></select>
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label for="courseId" class="form-label">Vak</label>
                        <select name="courseId" id="courseId" data-load-source="{{select:url:short}}/{{url:part.module}}/course" data-parent-select="typeId" data-on-change="courseView" data-search data-disable-if-no-options data-default-no-load required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="momentId" class="form-label">Tijdstip</label>
                        <select name="momentId" id="momentId" data-load-source="{{select:url:short}}/{{url:part.module}}/moment" data-label="formatted.shortDescription" data-disable-if-no-options data-default-no-load required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="classgroupId" class="form-label">Klas</label>
                        <select name="classgroupId" id="classgroupId" data-load-source="{{select:url:short}}/informat/classgroup" data-disable-if-no-options data-default-no-load data-search required></select>
                    </div>

                    <div class="col-lg-9 col-12 mb-3">
                        <label for="informatStudentId" class="form-label">Leerling</label>
                        <select name="informatStudentId" id="informatStudentId" data-load-source="{{select:url:short}}/informat/studentByClass" data-label="formatted.fullNameReversed" data-on-change="studentView" data-parent-select="classgroupId" data-disable-if-no-options data-default-no-load data-search required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3 d-none">
                        <label for="coursePerStudent" class="form-label">Vak</label>
                        <select name="coursePerStudent" id="coursePerStudent" data-load-source="{{select:url:short}}/{{url:part.module}}/coursePerStudent" data-disable-if-no-options data-search data-default-no-load></select>
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

                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="button" id="btnAdd" class="btn btn-success">Toevoegen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header btn-list">
                <button type="button" class="btn btn-danger ms-auto" id="btnRemove">Verwijderen</button>
            </div>
            <table role="table" id="tbl{{page:id}}" data-small data-no-info data-no-search data-no-paging data-checkbox>
                <thead>
                    <tr>
                        <td data-data="informatStudentId" data-width="20"></td>
                        <td data-data="fullNameReversed" data-orderable="0">Naam</td>
                        <td data-data="className" data-orderable="0" data-width="200">Klas</td>
                        <td data-data="courseName" data-orderable="0" data-width="300">Vak</td>
                        <td data-data="departmentId" data-visible="false"></td>
                        <td data-data="typeId" data-visible="false"></td>
                        <td data-data="courseId" data-visible="false"></td>
                        <td data-data="momentId" data-visible="false"></td>
                        <td data-data="classgroupId" data-visible="false"></td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</form>