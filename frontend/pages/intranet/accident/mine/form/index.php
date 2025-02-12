<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto">
    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Informatie leerling en ongeval</h1>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <label class="form-label" for="schoolId">School</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>
            </div>

            <div class="col-lg-4 mb-3">
                <label class="form-label" for="informatSubgroupId">Klas</label>
                <select name="informatSubgroupId" id="informatSubgroupId" data-load-source="{{select:url:short}}/informat/classgroup" data-load-value="id" data-load-label="name" data-parent-select="schoolId" data-extra="[schoolId={{user:mainSchoolId}}]" required></select>
            </div>

            <div class="col-lg-4 mb-3">
                <label class="form-label" for="informatStudentId">Leerling</label>
                <select name="informatStudentId" id="informatStudentId" data-load-source="{{select:url:short}}/informat/studentByClass" data-load-value="id" data-load-label="formatted.fullNameReversed" data-parent-select="informatSubgroupId" required></select>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label class="form-label" for="description">Beschrijving ongeval</label>
                <textarea name="description" id="description" rows="10" class="form-control" required></textarea>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Tijd en locatie</h1>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <label class="form-label" for="datetime">Vond plaats op</label>
                <input type="datetime-local" name="datetime" id="datetime" class="form-control" required>
            </div>

            <div class="col-lg-6 mb-3">
                <label class="form-label" for="location">Locatie van het ongeval</label>
                <select name="location" id="location" data-load-source="{{select:url:short}}/{{url:part.module}}/location" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" required data-on-change="locationView" data-render-item="renderOptgroupItem"></select>
            </div>
        </div>

        <div class="row d-none" id="location-O">
            <div class="col mb-3">
                <label class="form-label" for="exactLocation">Exacte locatie</label>
                <input type="text" name="exactLocation" id="exactLocation" class="form-control" required>
            </div>

            <div class="col mb-3">
                <label class="form-label" for="transport">Wat was het gebruikte vervoersmiddel?</label>
                <input type="text" name="transport" id="transport" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Betrokkenheid andere partijen</h1>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label" for="party">Ongeval te wijten aan...</label>
                <select name="party" id="party" data-load-source="{{select:url:short}}/{{url:part.module}}/party" data-load-value="id" data-load-label="name" required data-on-change="partyView"></select>
            </div>
        </div>

        <div class="row d-none" id="party-E">
            <div class="col-lg-5 mb-3">
                <label class="form-label" for="partyExternalName">Naam</label>
                <input type="text" name="partyExternalName" id="partyExternalName" class="form-control" required />
            </div>

            <div class="col-lg-5 mb-3">
                <label class="form-label" for="partyExternalFirstName">Voornaam</label>
                <input type="text" name="partyExternalFirstName" id="partyExternalFirstName" class="form-control" required />
            </div>

            <div class="col-lg-2 mb-3">
                <label class="form-label" for="partyExternalSex">Geslacht</label>
                <select name="partyExternalSex" id="partyExternalSex">
                    <option value="M">Man</option>
                    <option value="F">Vrouw</option>
                    <option value="X">X</option>
                </select>
            </div>

            <div class="col-lg-8 mb-3">
                <label class="form-label" for="partyExternalStreet">Straat</label>
                <input type="text" name="partyExternalStreet" id="partyExternalStreet" class="form-control" required />
            </div>

            <div class="col-lg-2 mb-3">
                <label class="form-label" for="partyExternalNumber">Huisnummer</label>
                <input type="text" name="partyExternalNumber" id="partyExternalNumber" class="form-control" required />
            </div>

            <div class="col-lg-2 mb-3">
                <label class="form-label" for="partyExternalBus">Bus</label>
                <input type="text" name="partyExternalBus" id="partyExternalBus" class="form-control" />
            </div>

            <div class="col-lg-2 mb-3">
                <label class="form-label" for="partyExternalZipcode">Postcode</label>
                <input type="text" name="partyExternalZipcode" id="partyExternalStreet" class="form-control" required />
            </div>

            <div class="col-lg-8 mb-3">
                <label class="form-label" for="partyExternalCity">Gemeente</label>
                <input type="text" name="partyExternalCity" id="partyExternalCity" class="form-control" required />
            </div>

            <div class="col-lg-2 mb-3">
                <label class="form-label" for="partyExternalCountryId">Land</label>
                <select name="partyExternalCountryId" id="partyExternalCountryId" data-load-source="{{select:url:short}}/country" data-load-value="id" data-load-label="translatedName" required></select>
            </div>

            <div class="col-lg-8 mb-3">
                <label class="form-label" for="partyExternalCompany">Verzekering</label>
                <input type="text" name="partyExternalCompany" id="partyExternalCompany" class="form-control" required />
            </div>

            <div class="col-lg-4 mb-3">
                <label class="form-label" for="partyExternalPolicyNumber">Polisnummer</label>
                <input type="text" name="partyExternalPolicyNumber" id="partyExternalPolicyNumber" class="form-control" />
            </div>
        </div>

        <div class="row d-none" id="party-O">
            <div class="col-lg-8 mb-3">
                <label class="form-label" for="partyOtherFullName">Volledige naam</label>
                <input type="text" name="partyOtherFullName" id="partyOtherFullName" class="form-control" required />
            </div>

            <div class="col-lg-4 mb-3">
                <label class="form-label" for="partyOtherBirthDay">Geboortedatum</label>
                <input type="date" role="datepicker" name="partyOtherBirthDay" id="partyOtherBirthDay" class="form-control" required />
            </div>

            <div class="col mb-3">
                <label class="form-label" for="partyOtherFullAddress">Volledige adres</label>
                <input type="text" name="partyOtherFullAddress" id="partyOtherFullAddress" class="form-control" required />
            </div>
        </div>

        <div class="row d-none" id="party-I">
            <div class="col mb-3">
                <label class="form-label" for="partyInstallReason">Nader te bepalen</label>
                <input type="text" name="partyInstallReason" id="partyInstallReason" class="form-control" required />
            </div>
        </div>

        <div class="row">
            <div class="col" id="chbPolice" role="checkbox" data-type="checkbox" data-name="police" data-text="Tussenkomst Politie" data-on-change="policeView"></div>
        </div>

        <div class="row d-none" id="police-Y">
            <div class="col-lg-8 mb-3">
                <label class="form-label" for="policeName">Naam agent(e)</label>
                <input type="text" name="policeName" id="policeName" class="form-control" required />
            </div>

            <div class="col-lg-4 mb-3">
                <label class="form-label" for="policePVNumber">Eventueel PV-nummer</label>
                <input type="text" name="policePVNumber" id="policePVNumber" class="form-control" />
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <h1 class="card-title">Toezicht</h1>
        </div>

        <div class="row">
            <div class="col" id="chbSupervision" role="checkbox" data-type="checkbox" data-name="supervision" data-text="Er was toezicht" data-on-change="supervisionView"></div>
        </div>

        <div class="row d-none" id="supervision-Y">
            <div class="col mb-3">
                <label class="form-label" for="informatSupervisorId">Naam toezichter</label>
                <select name="informatSupervisorId" id="informatSupervisorId" data-load-source="{{select:url:short}}/informat/employee" data-load-value="id" data-load-label="formatted.fullNameReversed" required></select>
            </div>
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