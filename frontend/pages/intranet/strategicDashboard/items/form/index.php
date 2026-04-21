<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School (geen = algemeen)</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-default-value="{{user:mainSchoolId}}"></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="typeId">Type</label>
                    <select name="typeId" id="typeId" data-load-source="{{select:url:full}}Type" required></select>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-12 mb-3">
                    <label for="minimum" class="form-label">Minimum</label>
                    <input type="number" name="minimum" id="minimum" class="form-control" />
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label for="target" class="form-label">Streefdoel</label>
                    <input type="number" name="target" id="target" class="form-control" />
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label for="width" class="form-label">Breedte</label>
                    <input type="number" name="width" id="width" min="2" max="12" step="2" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="canEditUserId" class="form-label">Mogen bewerken</label>
                    <select name="canEditUserId" id="canEditUserId" data-load-source="{{select:url:short}}/user" data-label="formatted.fullNameReversed" multiple data-search></select>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>