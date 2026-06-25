<?php
const HISTORY_TEMPLATE = "   <div class='row mb-1 border-bottom-wide'>
                                    <div class='col-12 mb-1'>
                                        <h3>@formatted.action@: @linked.creatorUser.formatted.fullNameReversed@ (@formatted.datetime.display@)</h3>
                                        <h4>Oude gegevens</h4>
                                        @formatted.changedData@
                                    </div>
                                </div>";
?>
<div class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form action="{{form:url:full}}" class="card" method="post" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
            <div class="card-body">
                <h1 class="card-title">Personalia</h1>

                <div class="row">
                    <div class="col-lg-4 col-12 mb-3">
                        <label class="form-label" for="name">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{url:param.name}}" required />
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label class="form-label" for="firstName">Voornaam</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" value="{{url:param.firstName}}" required />
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label class="form-label" for="sex">Geslacht</label>
                        <select name="sex" id="sex" data-load-source="{{select:url:short}}/{{url:part.module}}/sex" data-default-value="{{url:param.sex}}" required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="insz">Rijksregisternummer</label>
                        <input type="text" name="insz" id="insz" class="form-control" value="{{url:param.insz}}" data-mask="00.00.00-000.00" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="phone">GSM-nummer (persoonlijk)</label>
                        <input type="text" name="phone" id="phone" class="form-control" data-mask="+32 400/00.00.00" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="email" class="form-label">E-mail (persoonlijk)</label>
                        <input type="email" name="email" id="email" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="card-body">
                <h1 class="card-title">Werk</h1>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="statusId">Status</label>
                        <select name="statusId" id="statusId" data-load-source="{{select:url:short}}/{{url:part.module}}/status"></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="informatId" class="form-label">Intern nummer</label>
                        <input type="number" name="informatId" id="informatId" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="schoolEmail" class="form-label">E-mail (school)</label>
                        <input type="email" name="schoolEmail" id="schoolEmail" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="cv" class="form-label">CV</label>
                        <input type="file" name="cv" id="cv" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="certificate" class="form-label">Diploma</label>
                        <input type="text" name="certificate" id="certificate" class="form-control" />
                    </div>

                    <div class="col-12" id="chbProof" role="checkbox" data-type="checkbox" data-name="proof" data-text="Heeft een bekwaamheidsbewijs"></div>
                </div>
            </div>

            <div class="card-body">
                <h1 class="card-title">Opdracht</h1>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="start" class="form-label">Startdatum</label>
                        <input role="datepicker" name="start" id="start" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="end" class="form-label">Einddatum</label>
                        <input role="datepicker" name="end" id="end" class="form-control" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="laptopUsage" class="form-label">Laptopgebruik</label>
                        <select name="laptopUsage" id="laptopUsage" data-load-source="{{select:url:short}}/{{url:part.module}}/laptopUsage"></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="roleId" class="form-label">Functie(s)</label>
                        <select name="roleId" id="roleId" data-load-source="{{select:url:short}}/{{url:part.module}}/role" data-render-item="renderOptgroupItem" multiple></select>
                    </div>

                    <div class="col-12" id="chbFunctionDescriptionReceived" role="checkbox" data-type="checkbox" data-name="functionDescriptionReceived" data-text="Functiebeschrijving ontvangen"></div>
                </div>

                <div class="row">
                    <label for="info" class="form-label">Info</label>
                    <input type="text" role="tinymce" name="info" id="info" class="form-control">
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="button" class="btn" onclick="history.back();">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>


    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Geschiedenis</h1>
            </div>
            <div class="card-body" role="list" id="lstHistory{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/{{url:part.page}}History/{{url:part.id}}" data-template="<?= HISTORY_TEMPLATE; ?>"></div>
        </div>
    </div>
</div>