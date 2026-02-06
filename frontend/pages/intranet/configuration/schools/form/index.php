<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label" for="color">Kleur</label>
                    <input type="color" name="color" id="color" class="form-control" />
                </div>

                <div class="col-12 col-lg-5 mb-3" id="chbVirtual" role="checkbox" data-type="checkbox" data-name="virtual" data-text="Virtuele school" data-on-change="virtualView"></div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="parentSchoolId" class="form-label">Hoofdschool</label>
                    <select name="parentSchoolId" id="parentSchoolId" data-load-source="{{select:url:short}}/school/all" data-label="formatted.nameWithParent"></select>
                </div>
            </div>

            <div class="mb-3 alert alert-important alert-warning d-none" role="alert" id="parentSchool-warning">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="icon ti ti-alert-triangle"></i>
                    </div>

                    <div>
                        De gegevens die hieronder niet ingevuld worden, worden automatisch vervangen door de gegevens ingevoerd bij de hoofdschool.<br />
                        Wil je dit niet? Zet dan hier de gegevens in of verwijder ze globaal uit de hoofdschool.
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="street">Straat</label>
                    <input type="text" name="street" id="street" class="form-control" />
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label class="form-label" for="number">Huisnummer</label>
                    <input type="number" name="number" id="number" class="form-control" />
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label class="form-label" for="bus">Bus</label>
                    <input type="text" name="bus" id="bus" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12 mb-3">
                    <label class="form-label" for="zipcode">Postcode</label>
                    <input type="text" name="zipcode" id="zipcode" class="form-control" />
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="city">Gemeente</label>
                    <input type="text" name="city" id="city" class="form-control" />
                </div>

                <div class="col-lg-3 col-12 mb-3">
                    <label class="form-label" for="countryId">Land</label>
                    <select name="countryId" id="countryId" data-load-source="{{select:url:short}}/general/country" data-default-value="237" data-search></select>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12" id="chbImport" role="checkbox" data-type="checkbox" data-name="import" data-text="Importeren uit Informat"></div>
                <div class="col-12" id="chbSync" role="checkbox" data-type="checkbox" data-name="sync" data-text="Synchroniseren"></div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label for="syncEmployeeCompanyName" class="form-label">AD Werknemer - Bedrijf</label>
                    <input type="text" name="syncEmployeeCompanyName" id="syncEmployeeCompanyName" class="form-control" />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="syncStudentCompanyName" class="form-label">AD Student - Bedrijf</label>
                    <input type="text" name="syncStudentCompanyName" id="syncStudentCompanyName" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="syncEmployeeOU" class="form-label">AD Werknemer - OU</label>
                    <input type="text" name="syncEmployeeOU" id="syncEmployeeOU" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="syncStudentOU" class="form-label">AD Student - OU</label>
                    <input type="text" name="syncStudentOU" id="syncStudentOU" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="adJobTitlePrefix">AD - Job Title Prefix</label>
                    <input type="text" name="adJobTitlePrefix" id="adJobTitlePrefix" class="form-control" required />
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="adOuPart">AD - OU Part</label>
                    <input type="text" name="adOuPart" id="adOuPart" class="form-control" />
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="adSecGroupPart">AD - Security Group Part</label>
                    <input type="text" name="adSecGroupPart" id="adSecGroupPart" class="form-control" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <label for="syncUpdateMail" class="form-label">Update ontvangen over Sync</label>
                    <textarea name="syncUpdateMail" id="syncUpdateMail" class="form-control" rows="5"></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label" for="intuneOrderIdPrefix">Intune Order ID Prefix</label>
                    <input type="text" name="intuneOrderIdPrefix" id="intuneOrderIdPrefix" class="form-control" />
                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label" for="jamfIpadPrefix">JAMF iPad Prefix</label>
                    <input type="text" name="jamfIpadPrefix" id="jamfIpadPrefix" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>