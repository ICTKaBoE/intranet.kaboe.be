<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="alias">Alias</label>
                    <input type="text" name="alias" id="alias" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="alias"></div>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="type">Type</label>
                    <select name="type" id="type" data-load-source="{{select:url:full}}Type" data-load-value="id" data-load-label="name" data-on-change="changeLocationType" data-default-value="HW" required></select>
                    <div class="invalid-feedback" data-feedback-input="type"></div>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="startId">Startlocatie</label>
                    <select name="startId" id="startId" data-load-source="[HW@{{select:url:short}}/user/address;WW@{{select:url:short}}/school]" data-load-value="id" data-load-label="[HW@formatted.address;WW@formatted.addressWithSchool]" data-default-details="HW" required></select>
                    <div class="invalid-feedback" data-feedback-input="startId"></div>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="endSchoolId">Eindbestemming</label>
                    <select name="endSchoolId" id="endSchoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
                    <div class="invalid-feedback" data-feedback-input="endSchoolId"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="distance">Afstand (enkele rit)</label>
                    <input type="number" name="distance" id="distance" class="form-control" step="0.1" min="0.5" required />
                    <div class="invalid-feedback" data-feedback-input="distance"></div>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label">Afstand (heen & weer)</label>
                    <input type="number" id="distanceDouble" class="form-control" disabled />
                </div>
            </div>

            <div class="row">
                <label for="color" class="form-label mb-0">Kleur</label>
                <div id="cinColor" role="colorinput" data-colors="blue|azure|indigo|purple|pink|red|orange|yellow|lime" data-input-name="color" data-default-value="blue"></div>
                <div class="invalid-feedback" data-feedback-input="color"></div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>