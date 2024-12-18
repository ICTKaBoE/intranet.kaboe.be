<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row" data-prefill-id="{{url:part.id}}">
    <div class="col-12 col-lg-9">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title d-block">Aanvullen/Aanpassen</h2>
            </div>

            <div class="card-body"></div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Details</h2>
            </div>

            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label mb-0" for="formatted.number">Nummer</label>
                    <input type="text" name="formatted.number" id="formatted.number" class="form-control" readonly>
                    <div class="invalid-feedback" data-feedback-input="formatted.number"></div>
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1" for="status">Status</label>
                    <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name"></select>
                    <div class="invalid-feedback" data-feedback-input="status"></div>
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" disabled></select>
                    <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                </div>

                <div class="mb-2">
                    <label class="form-label mb-1" for="creatorUserId">Aangegeven door</label>
                    <select name="creatorUserId" id="creatorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" disabled></select>
                    <div class="invalid-feedback" data-feedback-input="creatorUserId"></div>
                </div>
            </div>
        </div>
    </div>
</form>