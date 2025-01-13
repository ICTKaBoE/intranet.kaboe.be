<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>
                    <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="name">Naam (school niet in naam zetten)</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="name"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12" id="chbFullDay" role="checkbox" data-type="checkbox" data-name="fullDay" data-text="Volledige dag"></div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="start">Startdatum</label>
                    <input type="datetime-local" name="start" id="start" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="start"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="end">Einddatum</label>
                    <input type="datetime-local" name="end" id="end" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="end"></div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>