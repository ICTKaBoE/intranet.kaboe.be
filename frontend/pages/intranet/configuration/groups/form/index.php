<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="name"></div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="m365GroupId">M365 Group Id</label>
                    <input type="text" name="m365GroupId" id="m365GroupId" class="form-control" />
                    <div class="invalid-feedback" data-feedback-input="m365GroupId"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="read" name="read" type="checkbox" />
                        <span class="form-check-label">Lezen</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="create" name="create" type="checkbox" />
                        <span class="form-check-label">Aanmaken</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="update" name="update" type="checkbox" />
                        <span class="form-check-label">Update</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="delete" name="delete" type="checkbox" />
                        <span class="form-check-label">Verwijderen</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="export" name="export" type="checkbox" />
                        <span class="form-check-label">Exporteren</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="changeSettings" name="changeSettings" type="checkbox" />
                        <span class="form-check-label">Instellingen wijzigen</span>
                    </label>
                    <label class="form-check form-switch">
                        <input class="form-check-input" id="admin" name="admin" type="checkbox" />
                        <span class="form-check-label">Administator</span>
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="members">Leden</label>
                    <select name="members" id="members" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" multiple data-search></select>
                    <div class="invalid-feedback" data-feedback-input="members"></div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>