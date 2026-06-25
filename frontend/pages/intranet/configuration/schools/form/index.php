<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto" data-prefill-id="{{url:part.id}}">
    <div class="card-header">
        <ul class='nav nav-tabs card-header-tabs' data-bs-toggle='tabs'>
            <li class='nav-item'><a href='#tab-general' class='nav-link active' data-bs-toggle='tab'>Algemeen</a></li>
            <li class='nav-item'><a href='#tab-address' class='nav-link' data-bs-toggle='tab'>Adres</a></li>
            <li class='nav-item'><a href='#tab-ad' class='nav-link' data-bs-toggle='tab'>AD</a></li>
            <li class='nav-item' id="tab-ad-employee-item"><a href='#tab-ad-employee' class='nav-link' data-bs-toggle='tab'>AD - Werknemer</a></li>
            <li class='nav-item' id="tab-ad-student-item"><a href='#tab-ad-student' class='nav-link' data-bs-toggle='tab'>AD - Student</a></li>
            <li class='nav-item'><a href='#tab-intune' class='nav-link' data-bs-toggle='tab'>Intune</a></li>
            <li class='nav-item'><a href='#tab-jamf' class='nav-link' data-bs-toggle='tab'>JAMF</a></li>
            <li class='nav-item'><a href='#tab-eetjemee' class='nav-link' data-bs-toggle='tab'>EetJeMee</a></li>
            <li class='nav-item'><a href='#tab-sms' class='nav-link' data-bs-toggle='tab'>Smartschool</a></li>
        </ul>
    </div>

    <div class="card-body">
        <div class='tab-content'>
            <div class='tab-pane active show' id='tab-general'>
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
                            De gegevens die hierna niet ingevuld worden (ook in andere tabbladen), worden automatisch vervangen door de gegevens ingevoerd bij de hoofdschool.<br />
                            Wil je dit niet? Vul dan hier de gegevens in of verwijder ze globaal uit de hoofdschool.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12" id="chbImport" role="checkbox" data-type="checkbox" data-name="import" data-text="Importeren uit Informat"></div>
                    <div class="col-12" id="chbSyncEmployee" role="checkbox" data-type="checkbox" data-name="syncEmployee" data-text="Personeel synchroniseren naar ActiveDirectory" data-on-change="syncEmployeeADView"></div>
                    <div class="col-12" id="chbSyncStudent" role="checkbox" data-type="checkbox" data-name="syncStudent" data-text="Leerlingen synchroniseren naar ActiveDirectory" data-on-change="syncStudentADView"></div>
                </div>
            </div>

            <div class='tab-pane' id='tab-address'>
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

            <div class="tab-pane" id="tab-ad">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="adOuPart">OU Part</label>
                        <input type="text" name="adOuPart" id="adOuPart" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="adSecGroupPart">Security Group Part</label>
                        <input type="text" name="adSecGroupPart" id="adSecGroupPart" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="tab-pane d-none" id="tab-ad-employee">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="syncEmployeeCompanyName" class="form-label">Bedrijf</label>
                        <input type="text" name="syncEmployeeCompanyName" id="syncEmployeeCompanyName" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="syncEmployeeOU" class="form-label">OU</label>
                        <input type="text" name="syncEmployeeOU" id="syncEmployeeOU" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="adJobTitlePrefix">Job Title Prefix</label>
                        <input type="text" name="adJobTitlePrefix" id="adJobTitlePrefix" class="form-control" required />
                    </div>
                </div>
            </div>

            <div class="tab-pane d-none" id="tab-ad-student">
                <div class="row">
                    <div class="col-12" id="chbTakeInAccountStartDate" role="checkbox" data-type="checkbox" data-name="takeInAccountStartDate" data-text="Rekening houden met start datum (nee = schooljaar)"></div>

                    <div class="col-12 mb-3">
                        <label for="syncStudentCompanyName" class="form-label">Bedrijf</label>
                        <input type="text" name="syncStudentCompanyName" id="syncStudentCompanyName" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="syncStudentOU" class="form-label">OU</label>
                        <input type="text" name="syncStudentOU" id="syncStudentOU" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="syncUpdateMail" class="form-label">Update ontvangen over Sync</label>
                        <textarea name="syncUpdateMail" id="syncUpdateMail" class="form-control" rows="5"></textarea>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-intune">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="intuneOrderIdPrefix">Order ID Prefix</label>
                        <input type="text" name="intuneOrderIdPrefix" id="intuneOrderIdPrefix" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-jamf">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="jamfIpadPrefix">iPad Prefix</label>
                        <input type="text" name="jamfIpadPrefix" id="jamfIpadPrefix" class="form-control" required />
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-eetjemee">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="eetjemeeKey">Platform KEY</label>
                        <input type="text" name="eetjemeeKey" id="eetjemeeKey" class="form-control" required />
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-sms">
                <div class="row">
                    <div class="col-12" id="chbSmsSyncClassTeachers" role="checkbox" data-type="checkbox" data-name="smsSyncClassTeachers" data-text="Klastitularissen zetten"></div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="smsGroupStudents">Smartschoolgroep - Leerlingen</label>
                        <input type="text" name="smsGroupStudents" id="smsGroupStudents" class="form-control" required />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="smsGroupEmployee">Smartschoolgroep - Personeel</label>
                        <input type="text" name="smsGroupEmployee" id="smsGroupEmployee" class="form-control" required />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer text-end">
        <button type="button" class="btn" onclick="history.back();">Annuleren</button>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </div>
</form>