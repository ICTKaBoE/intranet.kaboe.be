<div class="modal modal-blur fade show" id="modal-lend" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Lend" method="post" autocomplete="off" id="frm{{page:id}}Lend" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uitlenen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="lenderType">Type persoon</label>
                        <select name="lenderType" id="lenderType" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-load-value="id" data-load-label="name" data-on-change="setLender" required></select>
                        <div class="invalid-feedback" data-feedback-input="lenderType"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="lenderInformatId">Persoon</label>
                        <select name="lenderInformatId" id="lenderInformatId" data-load-source="[S@{{select:url:short}}/informat/student;T@{{select:url:short}}/informat/employee]" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[schoolId={{user:mainSchoolId}}]" required></select>
                        <div class="invalid-feedback" data-feedback-input="lenderInformatId"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Uitlenen</button>
            </div>
        </form>
    </div>
</div>