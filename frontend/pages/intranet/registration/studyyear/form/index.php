<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolyearId">Schooljaar</label>
                    <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/general/schoolyear" data-label="formatted.nameWithCurrent" required></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" required></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="name" class="form-label">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>