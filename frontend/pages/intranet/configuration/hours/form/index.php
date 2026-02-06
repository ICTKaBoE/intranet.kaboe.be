<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label for="schoolId" class="form-label">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school"></select>
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label" for="start">Start</label>
                    <input type="time" name="start" id="start" class="form-control" required />
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label" for="end">Einde</label>
                    <input type="time" name="end" id="end" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>