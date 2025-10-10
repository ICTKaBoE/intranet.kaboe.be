<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto">
    <div class="card-header">
        <h1 class="card-title fs-1" role="step-title"></h1>
    </div>

    <div class="card-body" data-step="1" data-title="Identificatie">
        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="schoolId">School</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/all" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-default-value="{{user:mainSchoolId}}" data-search required></select>
            </div>

            <div class="col-lg-8 col-12 mt-5" id="chbAnonymous" role="checkbox" data-default-value="false" data-on-change="anonymousView" data-type="checkbox" data-name="anonymous" data-text="Slachtoffer wenst anoniem te blijven"></div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="victimId" class="form-label">Slachtoffer</label>
                <select name="victimId" id="victimId" data-load-source="{{select:url:short}}/informat/employee" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[schoolId={{user:mainSchoolId}}]" data-search></select>
            </div>
        </div>
    </div>

    <div class="card-body" data-step="2" data-title="Basisgegevens">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="factsDateTime" class="form-label">Datum/Uur van de feiten (mag naar schatting)</label>
                <input type="datetime-local" name="factsDateTime" id="factsDateTime" class="form-control" required />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12 mb-3">
                <label for="identityParty" class="form-label">Identiteit van de derde (bvb. leerling, ouder, leverancier, ...)</label>
                <input type="text" name="identityParty" id="identityParty" class="form-control" />
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <label for="ageParty" class="form-label">Leeftijd van de derde</label>
                <input type="number" name="ageParty" id="ageParty" class="form-control" />
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3" id="chbWorkingHours" role="checkbox" data-default-value="true" data-type="checkbox" data-name="workingHours" data-text="Vonden de feiten plaats tijdens de werkuren?"></div>
        </div>
    </div>

    <div class="card-body" data-step="3" data-title="Feiten">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="form" class="form-label">Vorm van het ongewenst gedrag</label>
                <select name="form" id="form" data-load-source="{{select:url:short}}/{{url:part.module}}/form" data-load-value="id" data-load-label="name" data-on-change="formView" multiple required></select>
            </div>

            <div class="col-12 mb-3 d-none" id="form-O">
                <input type="text" name="formOther" id="formOther" class="form-control" required>
            </div>

            <div class="col-12 mb-3">
                <label for="out" class="form-label">Hoe kwam het ongewenst gedrag tot uiting</label>
                <select name="out" id="out" data-load-source="{{select:url:short}}/{{url:part.module}}/out" data-load-value="id" data-load-label="name" data-on-change="outView" multiple required></select>
            </div>

            <div class="col-12 mb-3 d-none" id="out-O">
                <input type="text" name="outOther" id="outOther" class="form-control" required>
            </div>

            <div class="col-12 mb-3">
                <label for="intention" class="form-label">De intentie van de derde, zoals beleefd door het slachtoffer</label>
                <select name="intention" id="intention" data-load-source="{{select:url:short}}/{{url:part.module}}/intention" data-load-value="id" data-load-label="name" data-on-change="intentionView" multiple required></select>
            </div>

            <div class="col-12 mb-3 d-none" id="intention-O">
                <input type="text" name="intentionOther" id="intentionOther" class="form-control" required>
            </div>

            <div class="col-12 mb-3">
                <label for="consequence" class="form-label">Wat zijn de gevolgen van het ongewenst gedrag</label>
                <select name="consequence" id="consequence" data-load-source="{{select:url:short}}/{{url:part.module}}/consequence" data-load-value="id" data-load-label="name" multiple></select>
            </div>
        </div>
    </div>

    <div class="card-body" data-step="4" data-title="Aanleiding">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="cause" class="form-label">Aanleiding van het ongewenst gedrag</label>
                <select name="cause" id="cause" data-load-source="{{select:url:short}}/{{url:part.module}}/cause" data-load-value="id" data-load-label="name" data-on-change="causeView" multiple></select>
            </div>
        </div>

        <div class="col-12 mb-3 d-none" id="cause-O">
            <input type="text" name="causeOther" id="causeOther" class="form-control" required>
        </div>
    </div>

    <div class="card-body" data-step="5" data-title="Schade of gevolg">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="damage" class="form-label">Schade of gevolg van het ongewenst gedrag aan personen</label>
                <select name="damage" id="damage" data-load-source="{{select:url:short}}/{{url:part.module}}/damage" data-load-value="id" data-load-label="name" multiple></select>
            </div>

            <div class="col-12 mb-3">
                <label for="damageKind" class="form-label">Soort schade of gevolg van het ongewenst gedrag</label>
                <select name="damageKind" id="damageKind" data-load-source="{{select:url:short}}/{{url:part.module}}/damageKind" data-load-value="id" data-load-label="name" multiple></select>
            </div>

            <div class="col-12 mb-3" id="chbPolice" role="checkbox" data-default-value="false" data-type="checkbox" data-name="police" data-text="Aangifte gedaan bij de politie"></div>
        </div>
    </div>

    <div class="card-body" data-step="6" data-title="Maatregelen en acties">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="actionsTaken" class="form-label">Welke acties zijn reeds genomen</label>
                <textarea name="actionsTaken" id="actionsTaken" class="form-control" rows=5></textarea>
            </div>

            <div class="col-12 mb-3">
                <label for="proposalEmployer" class="form-label">Voorstellen aan werkgever</label>
                <input type="text" name="proposalEmployer" id="proposalEmployer" class="form-control" />
            </div>

            <div class="col-12 mb-3">
                <label for="proposalConfidant" class="form-label">Voorstellen aan vertrouwenspersoon</label>
                <input type="text" name="proposalConfidant" id="proposalConfidant" class="form-control" />
            </div>

            <div class="col-12 mb-3">
                <label for="proposalPapsy" class="form-label">Voorstellen voor preventieadviseur psychosociale aspecten (PAPSY)</label>
                <input type="text" name="proposalPapsy" id="proposalPapsy" class="form-control" />
            </div>

            <div class="col-12 mb-3">
                <label for="proposalHead" class="form-label">Voorstellen voor de directe leidinggevende</label>
                <input type="text" name="proposalHead" id="proposalHead" class="form-control" />
            </div>
        </div>
    </div>

    <div class="card-footer btn-list">
        <button type="button" class="btn btn-primary me-auto d-none" id="btnPrevStep"><i class="icon ti ti-chevron-left"></i>Vorige stap</button>
        <button type="button" class="btn btn-primary ms-auto" id="btnNextStep">Volgende stap<i class="icon ti ti-chevron-right ms-2 me-n1"></i></button>
        <button type="submit" class="btn btn-primary ms-auto d-none" id="btnSubmit">Opslaan</button>
    </div>
</form>