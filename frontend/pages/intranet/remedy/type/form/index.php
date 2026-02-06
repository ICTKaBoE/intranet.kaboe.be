<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="departmentId">Afdeling</label>
                    <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-optgroup-attribute="schoolId" required></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label for="from" class="form-label">Zichtbaar vanaf</label>
                    <input type="date" name="from" id="from" class="form-control" />
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label for="until" class="form-label">Zichtbaar tot</label>
                    <input type="date" name="until" id="until" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="closeRegistrationAt" class="form-label">Sluit registratie op (voor de komende week)</label>
                    <select name="closeRegistrationAt" id="closeRegistrationAt">
                        <option value="0">Niet</option>
                        <option value="1">Maandag</option>
                        <option value="2">Dinsdag</option>
                        <option value="3">Woensdag</option>
                        <option value="4">Donderdag</option>
                        <option value="5">Vrijdag</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12" id="chbCourseDependsOnSkore" role="checkbox" data-default-value="false" data-type="checkbox" data-name="courseDependsOnSkore" data-text="Getoonde vakken hangen af van Skore?"></div>
                <div class="col-12" id="chbManualAssignDate" role="checkbox" data-default-value="false" data-type="checkbox" data-name="manualAssignDate" data-text="Leerling manueel toewijzen aan datum?"></div>
                <div class="col-12" id="chbOnComputer" role="checkbox" data-default-value="false" data-type="checkbox" data-name="onComputer" data-text="Mogelijkheid tot op computer?"></div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>