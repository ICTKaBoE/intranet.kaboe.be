<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto" data-prefill-id="{{url:part.id}}" data-locked-value="_lockedForm">
    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Algemene informatie</h1>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="schoolId">School</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/all" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-default-value="{{user:mainSchoolId}}" data-search required></select>
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <label for="absentUserId" class="form-label">Afwezige Leerkracht</label>
                <select name="absentUserId" id="absentUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" data-search required></select>
            </div>

            <div class="col-lg-2 col-12 mb-3">
                <label for="volume" class="form-label">Volume</label>
                <input type="text" name="volume" id="volume" class="form-control" data-mask="00/00" required />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12 mb-3">
                <label for="start" class="form-label">Startdatum</label>
                <input type="date" name="start" id="start" class="form-control" required />
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <label for="end" class="form-label">Einddatum</label>
                <input type="date" name="end" id="end" class="form-control" />
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Vervanger</h1>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" for="substituteBy">Leerkracht wordt vervangen door</label>
                <select name="substituteBy" id="substituteBy" data-load-source="{{select:url:short}}/{{url:part.module}}/substitute" data-load-value="id" data-load-label="name" required data-on-change="substituteByView"></select>
            </div>
        </div>

        <div class="row d-none" id="substituteBy-O">
            <div class="col mb-3">
                <input type="text" name="substituteByOther" id="substituteByOther" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" for="paymentOfSubstitute">Vervanger wordt betaald via</label>
                <select name="paymentOfSubstitute" id="paymentOfSubstitute" data-load-source="{{select:url:short}}/{{url:part.module}}/payment" data-load-value="id" data-load-label="name" required data-on-change="paymentOfSubstituteView"></select>
            </div>
        </div>

        <div class="row d-none" id="paymentOfSubstitute-O">
            <div class="col mb-3">
                <input type="text" name="paymentOfSubstituteOther" id="paymentOfSubstituteOther" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Overige</h1>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" for="absentNoteReceived">Ziektebriefje</label>
                <select name="absentNoteReceived" id="absentNoteReceived" data-load-source="{{select:url:short}}/{{url:part.module}}/note" data-load-value="id" data-load-label="name" required data-on-change="substituteView"></select>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" for="notes">Opmerkingen</label>
                <textarea name="notes" id="notes" rows="10" class="form-control"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col" id="chbFinished" role="checkbox" data-type="checkbox" data-name="finished" data-text="Afgewerkt"></div>
        </div>
    </div>

    <div class="card-footer d-flex py-3">
        <button type="button" class="btn btn-link link-secondary ms-auto" onclick="history.back();">Annuleren</button>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </div>
</form>

<script>
    let add = "<?= (\Router\Helpers::getId() === "add"); ?>";
</script>