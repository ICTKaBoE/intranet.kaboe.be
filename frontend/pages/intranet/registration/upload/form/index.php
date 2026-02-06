<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="alias">Alias</label>
                    <input type="text" name="alias" id="alias" class="form-control" required />
                </div>

                <div class="col-lg-4 col-12 mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" data-load-source="{{select:url:full}}Type" required></select>
                </div>

                <div class="col-lg-2 col-12 mb-3">
                    <label for="order" class="form-label">Volgorde</label>
                    <input type="number" name="order" id="order" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12 mb-3">
                    <label for="schoolyearId" class="form-label">Schooljaar</label>
                    <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/general/schoolyear" data-label="formatted.nameWithCurrent" data-on-change="setSchoolyear" required></select>
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label for="schoolId" class="form-label">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" required></select>
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label for="studyyearId" class="form-label">Leerjaar</label>
                    <select name="studyyearId" id="studyyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/studyyear" data-parent-select="schoolId" data-default-no-load required></select>
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label for="fieldId" class="form-label">Studierichting</label>
                    <select name="fieldId" id="fieldId" data-load-source="{{select:url:short}}/{{url:part.module}}/field" data-parent-select="studyyearId" data-default-no-load data-search required></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="name" class="form-label">Bestand</label>
                    <input type="file" role="file" name="name" id="name" class="form-control" />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>