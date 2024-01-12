<div class="row row-cards">
    <div class="col-md-5 m-auto">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card" data-prefill>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label" for="format">Nummer Formaat</label>
                        <input role="text" class="form-control" id="format" name="format" required>
                        <div class="invalid-feedback" data-feedback-input="format"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="assignToIds">Toewijzen aan</label>
                        <select class="form-control" name="assignToIds" id="assignToIds" data-load-source="{{select:url:short}}/users" data-load-value="id" data-load-label="fullName" multiple data-search required></select>
                        <div class="invalid-feedback" data-feedback-input="assignToIds"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="informNewToIds">Informeer bij nieuw ticket</label>
                        <select class="form-control" name="informNewToIds" id="informNewToIds" data-load-source="{{select:url:short}}/users" data-load-value="id" data-load-label="fullName" multiple data-search required></select>
                        <div class="invalid-feedback" data-feedback-input="informNewToIds"></div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>
</div>