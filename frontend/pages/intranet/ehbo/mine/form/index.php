<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto" data-prefill-id="{{url:part.id}}">
    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Algemene informatie</h1>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="schoolId">School</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/all" data-default-value="{{user:mainSchoolId}}" data-search required></select>
            </div>

            <div class="col-lg-8 col-12 mb-3">
                <label for="place" class="form-label">Plaats of afdeling van ongeval/onwel</label>
                <input type="text" name="place" id="place" class="form-control" required />
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label" for="description">Beschrijving/Omstandigheden ongeval/onwel</label>
                <select name="description" id="description" data-load-source="{{select:url:short}}/{{url:part.module}}/description" required data-on-change="descriptionView"></select>
            </div>
        </div>

        <div class="row d-none" id="description-O">
            <div class="col mb-3">
                <input type="text" name="descriptionOther" id="descriptionOther" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="firstHelpDateTime" class="form-label">Datum/Tijd eerste hulp</label>
                <input type="datetime-local" name="firstHelpDateTime" id="firstHelpDateTime" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label" for="firstHelp">Beschrijving eerste hulp</label>
                <select name="firstHelp" id="firstHelp" data-load-source="{{select:url:short}}/{{url:part.module}}/firstHelp" required data-on-change="firstHelpView"></select>
            </div>
        </div>

        <div class="row d-none" id="firstHelp-O">
            <div class="col mb-3">
                <input type="text" name="firstHelpOther" id="firstHelpOther" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label" for="victimType">Slachtoffer is een</label>
                <select name="victimType" id="victimType" data-load-source="{{select:url:short}}/{{url:part.module}}/victimType" data-on-change="setVictim" required></select>
            </div>

            <div class="col mb-3">
                <label class="form-label" for="victimId">Persoon</label>
                <select name="victimId" id="victimId" data-load-source="[S@{{select:url:short}}/informat/student;E@{{select:url:short}}/informat/employee]" data-label="formatted.fullNameReversed" data-extra="[schoolId={{user:mainSchoolId}}]" data-default-no-load data-search required></select>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="firstHelper" class="form-label">Hulpverlener (Naam + Voornaam)</label>
                <input type="text" name="firstHelper" id="firstHelper" class="form-control" required />
            </div>

            <div class="col-12 mb-3">
                <label for="witness" class="form-label">Getuige (Naam + Voornaam)</label>
                <input type="text" name="witness" id="witness" class="form-control" required />
            </div>
        </div>
    </div>

    <div class="card-footer text-end">
        <button type="button" class="btn" onclick="history.back();">Annuleren</button>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </div>
</form>