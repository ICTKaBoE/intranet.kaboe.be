<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="m365GroupId">M365 Group Id</label>
                    <input type="text" name="m365GroupId" id="m365GroupId" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="members">Leden</label>
                    <select name="members" id="members" data-load-source="{{select:url:short}}/user" data-label="formatted.fullNameReversed" multiple data-search></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="applications">Applicaties</label>
                    <select name="applications" id="applications" data-load-source="{{select:url:short}}/navigation/extended" data-optgroup="parentId" data-render-item="renderOptgroupItem" multiple data-search></select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="links">Links</label>
                    <select name="links" id="links" data-load-source="{{select:url:short}}/navigation/links" multiple data-search></select>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>