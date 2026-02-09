<?php
const LIST_TEMPLATE = " <div class='datagrid-item'>
                            <div class='datagrid-item fw-bold'>#title#</div>
                            <div class='datagrid-content'>#content#</div>
                        </div>";
?>

<div class="row">
    <div class="col-lg-9 col-12 mb-3">
        <form action="{{form:url:full}}Extranet-update" id="frm{{page:id}}" method="post" class="card" data-prefill-id="{{url:part.id}}" enctype="multipart/form-data" data-locked-value="_lockedForm">
            <input type="hidden" name="informatStudentId" id="informatStudentId" />
            <input type="hidden" name="status" id="status" />

            <div class="card-body">
                <div class="row">
                    <h1 class="card-title">Adresgegevens</h1>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="informatStudentAddressId">Adres</label>
                        <select name="informatStudentAddressId" id="informatStudentAddressId" data-load-source="{{select:url:short}}/{{url:part.module}}/informatStudentAddress" data-label="formatted.full" data-default-no-load required></select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <h1 class="card-title">Contactpersoon (Ouder/Voogd)</h1>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="informatStudentRelationId">Naam</label>
                        <select name="informatStudentRelationId" id="informatStudentRelationId" data-load-source="{{select:url:short}}/{{url:part.module}}/informatStudentRelation" data-label="formatted.typeWithFullNameReversed" data-default-no-load required></select>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="informatStudentEmailId">E-mail</label>
                        <select name="informatStudentEmailId" id="informatStudentEmailId" data-load-source="{{select:url:short}}/{{url:part.module}}/informatStudentEmail" data-label="formatted.typeWithEmail" data-default-no-load required></select>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="informatStudentNumberId">GSM/Telefoon</label>
                        <select name="informatStudentNumberId" id="informatStudentNumberId" data-load-source="{{select:url:short}}/{{url:part.module}}/informatStudentNumber" data-label="formatted.details" data-default-no-load required></select>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <label class="form-label" for="informatStudentBankId">Bank</label>
                        <select name="informatStudentBankId" id="informatStudentBankId" data-load-source="{{select:url:short}}/{{url:part.module}}/informatStudentBank" data-label="formatted.details" data-default-no-load required></select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <h1 class="card-title">Documenten</h1>
                    <h3 class="card-subtitle">Het uploaden van nieuwe documenten overschrijft bestaande documenten (zie kader "Documenten" rechts of onderaan)</h3>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="documentB" class="form-label">Geneeskundig getuigschrift (gedeelte B)</label>
                        <input type="file" name="documentB" id="documentB" class="form-control" accept=".pdf" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="documentC" class="form-label">Informatieblad (gedeelte C)</label>
                        <input type="file" name="documentC" id="documentC" class="form-control" accept=".pdf" />
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="button" class="btn btn-secondary ms-auto" id="btnClose">Sluiten zonder gevolg</button>
                <button type="submit" class="btn btn-success">Opslaan</button>
            </div>
        </form>
    </div>

    <div class="col-lg-3 col-12">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Informatie</h2>
            </div>

            <div class="card-body">
                <div class="datagrid" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Documenten</h2>
            </div>

            <div class="card-body">
                <div class="datagrid" role="list" id="lst{{page:id}}Attachments" data-source="{{list:url:full}}Attachments/{{url:part.id}}" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>
    </div>
</div>