<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto" data-prefill-id="{{url:part.id}}">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="schoolId">School</label>
                <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/all" data-default-value="{{user:mainSchoolId}}" data-search required></select>
            </div>

            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="buildingId">Gebouw</label>
                <select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/management/building/" data-extra="[schoolId={{user:mainSchoolId}}]" required data-parent-select="schoolId"></select>
            </div>

            <div class="col-12 col-lg-4 mb-3">
                <label class="form-label" for="roomId">Lokaal</label>
                <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/management/room/" data-label="formatted.name" required data-parent-select="buildingId"></select>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-10 col-12 mb-3">
                <label for="name" class="form-label">Naam (bvb lamineertoestel, lijmpistool, ...)</label>
                <input type="text" name="name" id="name" class="form-control" required />
            </div>

            <div class="col-lg-2 col-12 mb-3">
                <label for="amount" class="form-label">Aantal</label>
                <input type="number" name="amount" id="amount" class="form-control" required />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12 mb-3">
                <label for="brand" class="form-label">Merk (bvb Stanley)</label>
                <input type="text" name="brand" id="brand" class="form-control" required />
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <label for="model" class="form-label">Model (bvb A3T4ED)</label>
                <input type="text" name="model" id="model" class="form-control" required />
            </div>
        </div>

        <div class="row">
            <div class="col-12" id="chbOwnedBySchool" role="checkbox" data-default-value="true" data-type="checkbox" data-on-change="ownedBySchoolView" data-name="ownedBySchool" data-text="Eigendom van de school?"></div>
            <div class="col-12 mb-3" id="chbSchoolTakesOwnership" role="checkbox" data-type="checkbox" data-on-change="schoolTakesOwnershipView" data-name="schoolTakesOwnership" data-text="Mag het in dienst gesteld worden en eigendom van de school worden?"></div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="manual" class="form-label">Handleiding in het Nederlands</label>
                <input type="file" name="manual" id="manual" class="form-control" disabled />
            </div>

            <div class="col-12 mb-3">
                <label for="ce" class="form-label">Foto van het kenplaatje met CE-logo</label>
                <input type="file" name="ce" id="ce" class="form-control" disabled />
            </div>
        </div>
    </div>

    <div class="card-footer text-end">
        <button type="button" class="btn" onclick="history.back();">Annuleren</button>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </div>
</form>