<div class="modal modal-blur fade show" id="modal-return" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Return" method="post" autocomplete="off" id="frm{{page:id}}Return" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terugbrengen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="returnerType">Type persoon</label>
                        <select name="returnerType" id="returnerType" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-load-value="id" data-load-label="name" data-on-change="setReturner" required></select>

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="returnerInformatId">Persoon</label>
                        <select name="returnerInformatId" id="returnerInformatId" data-load-source="[S@{{select:url:short}}/informat/student;T@{{select:url:short}}/informat/employee]" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[schoolId={{user:mainSchoolId}}]" data-default-no-load required></select>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Terugbrengen</button>
            </div>
        </form>
    </div>
</div>