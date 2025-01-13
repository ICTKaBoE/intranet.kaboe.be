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
                <div class="col-12" id="chbRead" role="checkbox" data-type="checkbox" data-name="read" data-text="Lezen"></div>
                <div class="col-12" id="chbCreate" role="checkbox" data-type="checkbox" data-name="create" data-text="Aanmaken"></div>
                <div class="col-12" id="chbUpdate" role="checkbox" data-type="checkbox" data-name="update" data-text="Update"></div>
                <div class="col-12" id="chbDelete" role="checkbox" data-type="checkbox" data-name="delete" data-text="Verwijderen"></div>
                <div class="col-12" id="chbExport" role="checkbox" data-type="checkbox" data-name="export" data-text="Exporteren"></div>
                <div class="col-12" id="chbChangeSettings" role="checkbox" data-type="checkbox" data-name="changeSettings" data-text="Instellingen wijzigen"></div>
                <div class="col-12" id="chbAdmin" role="checkbox" data-type="checkbox" data-name="admin" data-text="Administrator"></div>
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