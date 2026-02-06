<div class="modal modal-blur fade" id="modal-fast" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Fast" method="post" autocomplete="off" id="frm{{page:id}}Fast" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fast Track</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label mb-1" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-default-value="{{user:mainSchoolId}}"></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="informatSubgroupId">Klas</label>
                        <select name="informatSubgroupId" id="informatSubgroupId" data-load-source="{{select:url:short}}/informat/classgroup" data-parent-select="schoolId" data-extra="[schoolId={{user:mainSchoolId}}]" data-search data-default-no-load required></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="informatStudentId">Leerling</label>
                        <select name="informatStudentId" id="informatStudentId" data-load-source="{{select:url:short}}/informat/studentByClass" data-label="formatted.fullNameReversed" data-parent-select="informatSubgroupId" data-default-no-load data-search required></select>
                    </div>

                    <div class="col-12" id="chbPrint" role="checkbox" data-type="checkbox" data-name="print" data-text="Document afprinten voor de dokter"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary ms-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>
</div>