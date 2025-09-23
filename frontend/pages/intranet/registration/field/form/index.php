<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-12 mb-3">
                    <label class="form-label" for="schoolyearId">Schooljaar</label>
                    <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/schoolyear" data-load-value="id" data-load-label="formatted.nameWithCurrent" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-on-change="setSchoolyear" required></select>
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" required></select>
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label class="form-label" for="studyyearId">Leerjaar</label>
                    <select name="studyyearId" id="studyyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/studyyear" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-parent-select="schoolId" required></select>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-9 col-12 mb-3">
                    <label for="name" class="form-label">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label for="administrativeGroupNumber" class="form-label">Administratieve groep</label>
                    <input type="text" name="administrativeGroupNumber" id="administrativeGroupNumber" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>