<div class="card col-12">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill>
        <div class="card-header">
            <h1 class="card-title fs-1" role="step-title"></h1>
        </div>

        <div class="card-body" data-step="1" data-title="Identificatie">
            <fieldset class="form-fieldset">
                <legend>Leerlinginformatie</legend>
                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="name">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="firstName">Voornaam</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="callName">Roepnaam</label>
                        <input type="text" name="callName" id="callName" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="sex" required>Geslacht</label>
                        <select name="sex" id="sex" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=sex" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="birthDate">Geboortedatum</label>
                        <input type="text" role="datepicker" name="birthDate" id="birthDate" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="birthPlace">Geboorteplaats</label>
                        <input type="text" name="birthPlace" id="birthPlace" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="birthCountryId">Geboorteland</label>
                        <select name="birthCountryId" id="birthCountryId" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-value="237" data-search></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mt-5" id="chbHasInsz" role="checkbox" data-default-value="true" data-on-change="checkInsz" data-type="checkbox" data-name="hasInsz" data-text="Heeft een Belgisch Rijksregisternummer?"></div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="insz">Rijksregisternummer</label>
                        <input type="text" name="insz" id="insz" class="form-control" data-mask="00.00.00-000.00" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="nationalityId">Nationaliteit</label>
                        <select name="nationalityId" id="nationalityId" data-load-source="{{select:url:short}}/general/nationality" data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="phone">GSM-nummer</label>
                        <input type="text" name="phone" id="phone" class="form-control" data-mask="+32 400/00.00.00" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="email">E-mailadres</label>
                        <input type="text" name="email" id="email" class="form-control" />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Domicilieadres <h3>officiëel adres volgens paspoort, eveneens facturatieadres</h3>
                </legend>
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="addressStreet">Straat</label>
                        <input type="text" name="addressStreet" id="addressStreet" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="addressNumber">Huisnummer</label>
                        <input type="number" name="addressNumber" id="addressNumber" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="addressBus">Bus</label>
                        <input type="text" name="addressBus" id="addressBus" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="addressZipcode">Postcode</label>
                        <input type="text" name="addressZipcode" id="addressZipcode" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="addressCity">Gemeente</label>
                        <input type="text" name="addressCity" id="addressCity" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="addressCountryId">Land</label>
                        <select name="addressCountryId" id="addressCountryId" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-value="237" data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Aanvullende informatie</legend>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="subscriberRelation" class="form-label">Hoedanigheid van de persoon die de leerling komt registreren</label>
                        <select name="subscriberRelation" id="subscriberRelation" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" required data-default-no-value></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="doctorName" class="form-label">Naam van de huisarts</label>
                        <input type="text" class="form-control" id="doctorName" name="doctorName" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="doctorNumber" class="form-label">Telefoonnummer van de huisarts</label>
                        <input type="text" class="form-control" id="doctorNumber" name="doctorNumber" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="meansOfTransport" class="form-label">Voornaamste vervoersmiddel van de leerling</label>
                        <select name="meansOfTransport" id="meansOfTransport" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=transport" data-load-value="id" data-load-label="name" data-default-no-value required></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="personalReligion" class="form-label">Persoonlijke Levensbeschouwing</label>
                        <select name="personalReligion" id="personalReligion" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=religion" data-load-value="id" data-load-label="name" data-default-value="140" required></select>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="2" data-title="Administratie">
            <fieldset class="form-fieldset">
                <legend>Administratie</legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="livingWith" class="form-label">Leerling woont bij</label>
                        <select name="livingWith" id="livingWith" data-on-change="checkLivingWith" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=livingWith" data-load-value="id" data-load-label="name" required data-default-no-value></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3 d-none" id="divLivingWithO">
                        <label for="livingWithOther" class="form-label">Relatie van 'Andere' tot de leerling</label>
                        <input type="text" name="livingWithOther" id="livingWithOther" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3 d-none" id="divLivingWithI">
                        <label for="livingWithIName" class="form-label">Naam van de instelling</label>
                        <input type="text" name="livingWithIName" id="livingWithIName" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="diedParent" class="form-label">Is er een ouder overleden?</label>
                        <select name="diedParent" id="diedParent" multiple data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=diedParent" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Facturatiegegevens</legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="bankaccountName" class="form-label">Titularis van de rekening</label>
                        <input type="text" name="bankaccountName" id="bankaccountName" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="bankaccountIban" class="form-label">Rekeningnummer</label>
                        <input type="text" name="bankaccountIban" id="bankaccountIban" class="form-control" />
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="3" data-title="Contactgegevens">
            <div class="d-none" id="chbContactOpen1" role="checkbox" data-default-value="true" data-type="checkbox" data-name="contactOpen1"></div>
            <div class="d-none" id="chbContactOpen2" role="checkbox" data-type="checkbox" data-name="contactOpen2"></div>
            <div class="d-none" id="chbContactOpen3" role="checkbox" data-type="checkbox" data-name="contactOpen3"></div>
            <div class="d-none" id="chbContactOpen4" role="checkbox" data-type="checkbox" data-name="contactOpen4"></div>
            <div class="d-none" id="chbContactOpen5" role="checkbox" data-type="checkbox" data-name="contactOpen5"></div>
            <div class="d-none" id="chbContactOpen6" role="checkbox" data-type="checkbox" data-name="contactOpen6"></div>

            <fieldset class="form-fieldset" data-contact="1">
                <legend>Gegevens van de leerplichtverantwoordelijke 1 / ouder 1 van de leerling
                    <h3>De persoon waarbij de leerling gedomicilieerd is. Als de ouders niet gescheiden zijn, is dit in de meeste gevallen de vader.</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name1" class="form-label">Familienaam</label>
                        <input type="text" name="name1" id="name1" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName1" class="form-label">Voornaam</label>
                        <input type="text" name="firstName1" id="firstName1" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation1" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation1" id="relation1" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" required data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther1" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther1" id="relationOther1" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate1" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate1" id="phonePrivate1" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork1" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork1" id="phoneWork1" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile1" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile1" id="phoneMobile1" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email1" class="form-label">E-mailadres</label>
                        <input type="text" name="email1" id="email1" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" data-contact="2">
                <legend>Gegevens van de leerplichtverantwoordelijke 2 / ouder 2 van de leerling
                    <h3>In de meeste gevallen is dit de moeder</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name2" class="form-label">Familienaam</label>
                        <input type="text" name="name2" id="name2" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName2" class="form-label">Voornaam</label>
                        <input type="text" name="firstName2" id="firstName2" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation2" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation2" id="relation2" required data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther2" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther2" id="relationOther2" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate2" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate2" id="phonePrivate2" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork2" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork2" id="phoneWork2" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile2" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile2" id="phoneMobile2" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email2" class="form-label">E-mailadres</label>
                        <input type="text" name="email2" id="email2" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" data-contact="3">
                <legend>Gegevens van de 3de contactpersoon</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name3" class="form-label">Familienaam</label>
                        <input type="text" name="name3" id="name3" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName3" class="form-label">Voornaam</label>
                        <input type="text" name="firstName3" id="firstName3" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation3" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation3" id="relation3" required data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther3" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther3" id="relationOther3" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate3" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate3" id="phonePrivate3" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork3" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork3" id="phoneWork3" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile3" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile3" id="phoneMobile3" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email3" class="form-label">E-mailadres</label>
                        <input type="text" name="email3" id="email3" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" data-contact="4">
                <legend>Gegevens van de 4de contactpersoon</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name4" class="form-label">Familienaam</label>
                        <input type="text" name="name4" id="name4" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName4" class="form-label">Voornaam</label>
                        <input type="text" name="firstName4" id="firstName4" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation4" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation4" id="relation4" required data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther4" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther4" id="relationOther4" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate4" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate4" id="phonePrivate4" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork4" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork4" id="phoneWork4" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile4" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile4" id="phoneMobile4" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email4" class="form-label">E-mailadres</label>
                        <input type="text" name="email4" id="email4" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" data-contact="5">
                <legend>Gegevens van de 5de contactpersoon</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name5" class="form-label">Familienaam</label>
                        <input type="text" name="name5" id="name5" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName5" class="form-label">Voornaam</label>
                        <input type="text" name="firstName5" id="firstName5" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation5" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation5" id="relation5" required data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther5" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther5" id="relationOther5" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate5" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate5" id="phonePrivate5" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork5" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork5" id="phoneWork5" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile5" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile5" id="phoneMobile5" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email5" class="form-label">E-mailadres</label>
                        <input type="text" name="email5" id="email5" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" data-contact="6">
                <legend>Gegevens van de 6de contactpersoon</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="name6" class="form-label">Familienaam</label>
                        <input type="text" name="name6" id="name6" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="firstName6" class="form-label">Voornaam</label>
                        <input type="text" name="firstName6" id="firstName6" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relation6" class="form-label">Hoedanigheid (relatie tot de leerling)</label>
                        <select name="relation6" id="relation6" required data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=relation" data-load-value="id" data-load-label="name" data-default-no-value></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="relationOther6" class="form-label">Relatie tot de leerling</label>
                        <input type="text" name="relationOther6" id="relationOther6" class="form-control" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phonePrivate6" class="form-label">Telefoonnummer (privé)</label>
                        <input type="text" name="phonePrivate6" id="phonePrivate6" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneWork6" class="form-label">Telefoonnummer (werk)</label>
                        <input type="text" name="phoneWork6" id="phoneWork6" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="phoneMobile6" class="form-label">GSM-nummer</label>
                        <input type="text" name="phoneMobile6" id="phoneMobile6" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="email6" class="form-label">E-mailadres</label>
                        <input type="text" name="email6" id="email6" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <div class="row">
                <div class="col text-end">
                    <button type="button" class="btn btn-success" id="btnAddContact"><i class="icon ti ti-plus"></i>Toevoegen</button>
                </div>
            </div>
        </div>

        <div class="card-body" data-step="4" data-title="Adresgegevens" data-before-load="checkRelations">
            <div class="d-none" id="chbAddressOpen1" role="checkbox" data-default-value="true" data-type="checkbox" data-name="addressOpen1"></div>
            <div class="d-none" id="chbAddressOpen2" role="checkbox" data-type="checkbox" data-name="addressOpen2"></div>
            <div class="d-none" id="chbAddressOpen3" role="checkbox" data-type="checkbox" data-name="addressOpen3"></div>
            <div class="d-none" id="chbAddressOpen4" role="checkbox" data-type="checkbox" data-name="addressOpen4"></div>
            <div class="d-none" id="chbAddressOpen5" role="checkbox" data-type="checkbox" data-name="addressOpen5"></div>
            <div class="d-none" id="chbAddressOpen6" role="checkbox" data-type="checkbox" data-name="addressOpen6"></div>

            <fieldset class="form-fieldset" id="fs-address1">
                <legend>Adres van de <span class="text-green" id="relationValue1"></span>
                    <h3>De leerplichtverantwoordelijke 1 / ouder 1 van de leerling</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom1" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom1" id="copyFrom1" data-on-change="copy1">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication1" role="checkbox" data-type="checkbox" data-name="communication1" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street1" class="form-label">Straat</label>
                        <input type="text" name="street1" id="street1" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number1" class="form-label">Huisnummer</label>
                        <input type="number" name="number1" id="number1" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus1" class="form-label">Bus</label>
                        <input type="text" name="bus1" id="bus1" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode1" class="form-label">Postcode</label>
                        <input type="text" name="zipcode1" id="zipcode1" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city1" class="form-label">Gemeente</label>
                        <input type="text" name="city1" id="city1" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country1" class="form-label">Land</label>
                        <select name="country1" id="country1" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="fs-address2">
                <legend>Adres van de <span class="text-green" id="relationValue2"></span>
                    <h3>De leerplichtverantwoordelijke 2 / ouder 2 van de leerling</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom2" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom2" id="copyFrom2" data-on-change="copy2">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication2" role="checkbox" data-type="checkbox" data-name="communication2" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street2" class="form-label">Straat</label>
                        <input type="text" name="street2" id="street2" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number2" class="form-label">Huisnummer</label>
                        <input type="number" name="number2" id="number2" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus2" class="form-label">Bus</label>
                        <input type="text" name="bus2" id="bus2" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode2" class="form-label">Postcode</label>
                        <input type="text" name="zipcode2" id="zipcode2" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city2" class="form-label">Gemeente</label>
                        <input type="text" name="city2" id="city2" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country2" class="form-label">Land</label>
                        <select name="country2" id="country2" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="fs-address3">
                <legend>Adres van de <span class="text-green" id="relationValue3"></span>
                    <h3>De 3de contactpersoon</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom3" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom3" id="copyFrom3" data-on-change="copy3">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication3" role="checkbox" data-type="checkbox" data-name="communication3" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street3" class="form-label">Straat</label>
                        <input type="text" name="street3" id="street3" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number3" class="form-label">Huisnummer</label>
                        <input type="number" name="number3" id="number3" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus3" class="form-label">Bus</label>
                        <input type="text" name="bus3" id="bus3" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode3" class="form-label">Postcode</label>
                        <input type="text" name="zipcode3" id="zipcode3" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city3" class="form-label">Gemeente</label>
                        <input type="text" name="city3" id="city3" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country3" class="form-label">Land</label>
                        <select name="country3" id="country3" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="fs-address4">
                <legend>Adres van de <span class="text-green" id="relationValue4"></span>
                    <h3>De 4de contactpersoon</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom4" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom4" id="copyFrom4" data-on-change="copy4">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication4" role="checkbox" data-type="checkbox" data-name="communication4" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street4" class="form-label">Straat</label>
                        <input type="text" name="street4" id="street4" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number4" class="form-label">Huisnummer</label>
                        <input type="number" name="number4" id="number4" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus4" class="form-label">Bus</label>
                        <input type="text" name="bus4" id="bus4" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode4" class="form-label">Postcode</label>
                        <input type="text" name="zipcode4" id="zipcode4" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city4" class="form-label">Gemeente</label>
                        <input type="text" name="city4" id="city4" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country4" class="form-label">Land</label>
                        <select name="country4" id="country4" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="fs-address5">
                <legend>Adres van de <span class="text-green" id="relationValue5"></span>
                    <h3>De 5de contactpersoon</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom5" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom5" id="copyFrom5" data-on-change="copy5">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication5" role="checkbox" data-type="checkbox" data-name="communication5" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street5" class="form-label">Straat</label>
                        <input type="text" name="street5" id="street5" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number5" class="form-label">Huisnummer</label>
                        <input type="number" name="number5" id="number5" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus5" class="form-label">Bus</label>
                        <input type="text" name="bus5" id="bus5" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode5" class="form-label">Postcode</label>
                        <input type="text" name="zipcode5" id="zipcode5" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city5" class="form-label">Gemeente</label>
                        <input type="text" name="city5" id="city5" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country5" class="form-label">Land</label>
                        <select name="country5" id="country5" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="fs-address6">
                <legend>Adres van de <span class="text-green" id="relationValue6"></span>
                    <h3>De 6de contactpersoon</h3>
                </legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="copyFrom6" class="form-label">Adres overnemen van</label>
                        <select name="copyFrom6" id="copyFrom6" data-on-change="copy6">
                            <option value="_e_">Leeg (zelf in te vullen)</option>
                            <option value="_d_">Domicilieadres</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mt-5" id="chbCommunication6" role="checkbox" data-type="checkbox" data-name="communication6" data-text="Dit adres mag gebruikt worden als correspondentieadres?"></div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="street6" class="form-label">Straat</label>
                        <input type="text" name="street6" id="street6" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="number6" class="form-label">Huisnummer</label>
                        <input type="number" name="number6" id="number6" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="bus6" class="form-label">Bus</label>
                        <input type="text" name="bus6" id="bus6" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="zipcode6" class="form-label">Postcode</label>
                        <input type="text" name="zipcode6" id="zipcode6" class="form-control" required />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="city6" class="form-label">Gemeente</label>
                        <input type="text" name="city6" id="city6" class="form-control" required />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="country6" class="form-label">Land</label>
                        <select name="country6" id="country6" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search required></select>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="5" data-title="Schoolgegevens">
            <fieldset class="form-fieldset">
                <legend>Gegevens voor inschrijving in het huidige of komende schooljaar</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="schoolyearId" class="form-label">Schooljaar</label>
                        <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/schoolyear" data-load-value="id" data-load-label="formatted.nameWithCurrent" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-on-change="setSchoolyear" required></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="schoolId" class="form-label">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" required></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="studyyearId" class="form-label">Leerjaar</label>
                        <select name="studyyearId" id="studyyearId" data-load-source="{{select:url:short}}/{{url:part.module}}/studyyear" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-parent-select="schoolId" data-default-no-load required></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="fieldId" class="form-label">Studierichting</label>
                        <select name="fieldId" id="fieldId" data-load-source="{{select:url:short}}/{{url:part.module}}/field" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-parent-select="studyyearId" data-on-change="setField" data-default-no-load data-search required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="optionId" class="form-label">Keuzevak</label>
                        <select name="optionId" id="optionId" data-load-source="{{select:url:short}}/{{url:part.module}}/option" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-default-no-load data-hide-if-no-options data-search></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="talentId" class="form-label">Talentmodule</label>
                        <select name="talentId" id="talentId" data-load-source="{{select:url:short}}/{{url:part.module}}/talent" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-default-no-load data-hide-if-no-options data-search></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="clilId" class="form-label">CLIL (Content and Learning Integrated Language)</label>
                        <select name="clilId" id="clilId" data-load-source="{{select:url:short}}/{{url:part.module}}/clil" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-default-no-load data-hide-if-no-options data-search></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="sitWith" class="form-label">Wenst samen te zitten met</label>
                        <input type="text" name="sitWith" id="sitWith" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="sitNotWith" class="form-label">Wenst NIET samen te zitten met</label>
                        <input type="text" name="sitNotWith" id="sitNotWith" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="lastSchool" class="form-label">Laatste school waar de leerling les volgde was een</label>
                        <select name="lastSchool" id="lastSchool" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchool" data-load-value="id" data-load-label="name" data-default-value="N" data-on-change="checkLastSchool"></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="startAt" class="form-label">De leerling zal de lessen volgen vanaf</label>
                        <input type="text" role="datepicker" name="startAt" id="startAt" class="form-control" required />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" id="fsCurrentSchool">
                <legend>Gegevens van het lopende schooljaar (inschrijving na 1 september, niet van College ten Doorn)</legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="currentSchoolStudyyear" class="form-label">Gevolgd leerjaar</label>
                        <select name="currentSchoolStudyyear" id="currentSchoolStudyyear" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=currentSchoolStudyyear" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="currentSchoolField" class="form-label">Gevolgde studierichting</label>
                        <input type="text" name="currentSchoolField" id="currentSchoolField" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="currentSchoolName" class="form-label">Naam van de school</label>
                        <input type="text" name="currentSchoolName" id="currentSchoolName" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="currentSchoolStreet" class="form-label">Straat</label>
                        <input type="text" name="currentSchoolStreet" id="currentSchoolStreet" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="currentSchoolNumber" class="form-label">Nummer</label>
                        <input type="number" name="currentSchoolNumber" id="currentSchoolNumber" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="currentSchoolBus" class="form-label">Bus</label>
                        <input type="text" name="currentSchoolBus" id="currentSchoolBus" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="currentSchoolZipcode" class="form-label">Postcode</label>
                        <input type="text" name="currentSchoolZipcode" id="currentSchoolZipcode" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="currentSchoolCity" class="form-label">Gemeente</label>
                        <input type="text" name="currentSchoolCity" id="currentSchoolCity" class="form-control" />
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="currentSchoolCountryId" class="form-label">Land</label>
                        <select name="currentSchoolCountryId" id="currentSchoolCountryId" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-value="237" data-search required></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Gegevens voor de schoolorganisatie</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="mealMonday" class="form-label">Maaltijd op maandagmiddag</label>
                        <select name="mealMonday" id="mealMonday" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=meal" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="mealTuesday" class="form-label">Maaltijd op dinsdagmiddag</label>
                        <select name="mealTuesday" id="mealTuesday" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=meal" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="mealThursday" class="form-label">Maaltijd op donderdagmiddag</label>
                        <select name="mealThursday" id="mealThursday" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=meal" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="mealFriday" class="form-label">Maaltijd op vrijdagmiddag</label>
                        <select name="mealFriday" id="mealFriday" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=meal" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="lastSchoolB">
                <legend>Gegevens van de laatste school (het vorige schooljaar)</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastSchoolStudyyearB" class="form-label">Gevolgd leerjaar</label>
                        <select name="lastSchoolStudyyearB" id="lastSchoolStudyyearB" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolStudyyearB" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-9 col-12 mb-3">
                        <label for="lastSchoolNameB" class="form-label">Naam van de basisschool</label>
                        <input type="text" name="lastSchoolNameB" id="lastSchoolNameB" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="lastSchoolCountryB">Land</label>
                        <select name="lastSchoolCountryB" id="lastSchoolCountryB" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastSchoolZipcodeB" class="form-label">Postcode</label>
                        <input type="text" name="lastSchoolZipcodeB" id="lastSchoolZipcodeB" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolCityB" class="form-label">Gemeente</label>
                        <input type="text" name="lastSchoolCityB" id="lastSchoolCityB" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mt-5" id="chbLastSchoolHasCertificateB" role="checkbox" data-default-value="true" data-type="checkbox" data-name="lastSchoolHasCertificateB" data-text="Getuigschrift basisonderwijs behaald?"></div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolAdviceB" class="form-label">Advies</label>
                        <input type="text" name="lastSchoolAdviceB" id="lastSchoolAdviceB" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="lastSchoolCertificateReceivedB" class="form-label">Getuigschrift afgegeven</label>
                        <select name="lastSchoolCertificateReceivedB" id="lastSchoolCertificateReceivedB" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolCertificateReceivedB" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="lastSchoolBaSoCertificateReceivedB" class="form-label">BaSo-fiche afgegeven</label>
                        <select name="lastSchoolBaSoCertificateReceivedB" id="lastSchoolBaSoCertificateReceivedB" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolBaSoCertificateReceivedB" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="lastSchoolS">
                <legend>Gegevens van de laatste school (het vorige schooljaar)</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastStudyyearS" class="form-label">Gevolgd leerjaar</label>
                        <select name="lastStudyyearS" id="lastStudyyearS" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolStudyyearS" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-9 col-12 mb-3">
                        <label for="lastSchoolFieldS" class="form-label">Gevolgde studierichting</label>
                        <input type="text" name="lastSchoolFieldS" id="lastSchoolFieldS" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="lastSchoolNameS" class="form-label">Naam van de secundaire school</label>
                        <input type="text" name="lastSchoolNameS" id="lastSchoolNameS" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="lastSchoolCountryS">Land</label>
                        <select name="lastSchoolCountryS" id="lastSchoolCountryS" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastSchoolZipcodeS" class="form-label">Postcode</label>
                        <input type="text" name="lastSchoolZipcodeS" id="lastSchoolZipcodeS" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolCityS" class="form-label">Gemeente</label>
                        <input type="text" name="lastSchoolCityS" id="lastSchoolCityS" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolCertificateS" class="form-label">Behaald attest</label>
                        <select name="lastSchoolCertificateS" id="lastSchoolCertificateS" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolCertificateS" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolClauseS" class="form-label">Clausule</label>
                        <input type="text" name="lastSchoolClauseS" id="lastSchoolClauseS" class="form-control" />
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset" id="lastSchoolH">
                <legend>Gegevens van de laatste school (het vorige schooljaar)</legend>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastStudyyearH" class="form-label">Gevolgd jaar</label>
                        <select name="lastStudyyearH" id="lastStudyyearH" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=lastSchoolStudyyearH" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-9 col-12 mb-3">
                        <label for="lastSchoolFieldH" class="form-label">Gevolgde studierichting</label>
                        <input type="text" name="lastSchoolFieldH" id="lastSchoolFieldH" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="lastSchoolNameH" class="form-label">Naam van de hogeschool</label>
                        <input type="text" name="lastSchoolNameH" id="lastSchoolNameH" class="form-control" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-12 mb-3">
                        <label class="form-label" for="lastSchoolCountryH">Land</label>
                        <select name="lastSchoolCountryH" id="lastSchoolCountryH" data-load-source="{{select:url:short}}/general/country" data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>

                    <div class="col-lg-3 col-12 mb-3">
                        <label for="lastSchoolZipcodeH" class="form-label">Postcode</label>
                        <input type="text" name="lastSchoolZipcodeH" id="lastSchoolZipcodeH" class="form-control" />
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="lastSchoolCityH" class="form-label">Gemeente</label>
                        <input type="text" name="lastSchoolCityH" id="lastSchoolCityH" class="form-control" />
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="6" data-title="Privacy & Zorg">
            <fieldset class="form-fieldset">
                <legend>Gegevens die belangrijk zijn voor informatieveiligheid en privacy</legend>

                <div class="row">
                    <div class="col-12" id="chbPicture" role="checkbox" data-type="checkbox" data-on-change="checkPictures" data-name="picture" data-text="Mogen er foto’s van uw zoon/dochter (individueel of in groep) genomen worden tijdens activiteiten, excursies, enz. ?"></div>
                    <div class="col-12" id="chbClassPicture" role="checkbox" data-type="checkbox" data-on-change="checkPictures" data-name="classPicture" data-text="Mag uw zoon/dochter mee op de klasfoto staan?"></div>
                    <div class="col-12" id="chbPassInfo" role="checkbox" data-type="checkbox" data-name="passInfo" data-text="Mag de naam en het adres van de leerling doorgegeven worden aan hogescholen, universiteiten?"></div>
                    <div class="col-12" id="chbMeasurement" role="checkbox" data-type="checkbox" data-name="measurement" data-text="Mag de leerling deel nemen aan de meting van het welbevinden (via de app Appwel)?"></div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" id="fsPictures">
                <legend>Op welke van volgende platformen mogen we foto's of beeldmateriaal van uw dochter/zoon publiceren?</legend>

                <div class="row">
                    <div class="col-12" id="chbSmartschool" role="checkbox" data-type="checkbox" data-name="smartschool" data-text="Smartschool waarop enkel personeel, de verantwoordelijke van de leerling en/of de ouders en leerlingen kunnen aanmelden."></div>
                    <div class="col-12" id="chbWebsite" role="checkbox" data-type="checkbox" data-name="website" data-text="Onze schoolwebsite"></div>
                    <div class="col-12" id="chbSocials" role="checkbox" data-type="checkbox" data-name="socials" data-text="Alle platformen sociale media (Facebook, Twitter, ...) van onze school."></div>
                    <div class="col-12" id="chbNewsletter" role="checkbox" data-type="checkbox" data-name="newsletter" data-text="Onze digitale nieuwsbrief, promotiefolders van onze school en van onze scholengemeenschap KOM."></div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Gegevens die belangrijk zijn voor de leerlingbegeleiding</legend>

                <div class="row">
                    <div class="col-lg-4 col-12 mb-3">
                        <label for="homeLanguage" class="form-label">Wat is de hoofd thuistaal van de leerling?</label>
                        <select name="homeLanguage" id="homeLanguage" data-load-source="{{select:url:short}}/general/language" data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label for="homeLanguageMore" class="form-label">Welke talen worden door de leerling nog thuis gebruikt?</label>
                        <select name="homeLanguageMore" id="homeLanguageMore" data-load-source="{{select:url:short}}/general/language" multiple data-load-value="id" data-load-label="name" data-default-no-value data-search></select>
                    </div>

                    <div class="col-lg-4 col-12 mb-3">
                        <label for="howLongDutch" class="form-label">Hoe lang spreekt de leerling al Nederlands?</label>
                        <select name="howLongDutch" id="howLongDutch" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=howLongDutch" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12" id="chbProblemLearn" role="checkbox" data-type="checkbox" data-on-change="checkProblemLearn" data-name="problemLearn" data-text="Heeft de leerling leerproblemen?"></div>
                    <div class="col-12" id="chbProblemFamily" role="checkbox" data-type="checkbox" data-on-change="checkProblemFamily" data-name="problemFamily" data-text="Heeft de leerling familiale of persoonlijke problemen?"></div>
                    <div class="col-12" id="chbProblemHealth" role="checkbox" data-type="checkbox" data-on-change="checkProblemHealth" data-name="problemHealth" data-text="Heeft de leerling gezondheidsproblemen?"></div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" id="fsProblemLearn">
                <legend>Leren & Studeren: leerproblemen</legend>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="problemLearnProblem" class="form-label">Welke leerproblemen heeft de leerling?</label>
                        <select name="problemLearnProblem" id="problemLearnProblem" multiple data-on-change="checkProblemLearnProblem" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=problemLearnProblem" data-load-value="id" data-load-label="name"></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="problemLearnProblemOther" class="form-label">Specifieer de 'Andere' leerproblemen</label>
                        <input type="text" name="problemLearnProblemOther" id="problemLearnProblemOther" class="form-control" disabled />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3" id="chbProblemLearnCertificate" role="checkbox" data-type="checkbox" data-on-change="checkProblemLearnCertificate" data-name="problemLearnCertificate" data-text="Bent u in het bezit van een attest waarbij dit leerprobleem werd vastgesteld door een logo / arts / kinesist / CLB?"></div>

                    <div class="col-12 mb-3 d-none" id="problemLearnCertificateReceivedContainer">
                        <label for="problemLearnCertificateReceived" class="form-label">Hoe krijgt de school inzage in dit attest?</label>
                        <select name="problemLearnCertificateReceived" id="problemLearnCertificateReceived" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=problemLearnCertificateReceived" data-load-value="id" data-load-label="name"></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemLearnGuidance" class="form-label">Welke begeleiding heeft de leerling nodig voor dit leerprobleem?</label>
                        <textarea name="problemLearnGuidance" id="problemLearnGuidance" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemLearnExtra" class="form-label">Heeft de leerling last van leerproblemen?</label>
                        <textarea name="problemLearnExtra" id="problemLearnExtra" rows="5" class="form-control"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" id="fsProblemFamily">
                <legend>Socio-Emotioneel: Familiale en persoonlijke problemen</legend>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemFamilyFamily" class="form-label">Heeft de leerling familiale problemen?</label>
                        <textarea name="problemFamilyFamily" id="problemFamilyFamily" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemFamilyPersonal" class="form-label">Heeft de leerling persoonlijke problemen?</label>
                        <textarea name="problemFamilyPersonal" id="problemFamilyPersonal" rows="5" class="form-control"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset d-none" id="fsProblemHealth">
                <legend>Gezondheid: medicatie en medische gegevens</legend>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemHealthNotify" class="form-label">Van welke gegevens over de gezondheid van de leerling wenst u de school op de hoogte te brengen?</label>
                        <textarea name="problemHealthNotify" id="problemHealthNotify" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="problemHealthDoDont" class="form-label">Wat moeten we doen? Wat mogen we niet doen? Dit om zo gepast mogelijk te reageren.</label>
                        <textarea name="problemHealthDoDont" id="problemHealthDoDont" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mt-5 mb-3" id="chbProblemHealthMedication" role="checkbox" data-type="checkbox" data-on-change="checkProblemHealthMedication" data-name="problemHealthMedication" data-text="Neemt de leerling hiervoor medicatie?"></div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="problemHealthMedicationWhat" class="form-label">Specifieer de medicatie</label>
                        <textarea name="problemHealthMedicationWhat" id="problemHealthMedicationWhat" rows="5" class="form-control" disabled></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3" id="chbProblemHealthConsult" role="checkbox" data-type="checkbox" data-name="problemHealthConsult" data-text="Gaat u ermee akkoord dat de CLB-medewerker / arts informatie opvraagt en hierover overlegt indien nodig?"></div>
                </div>

                <div class="row">
                    <label for="problemHealthInternConsultConsult" class="form-label">Wenst u bepaalde gegevens verder te bespreken met de klassenleraar, de leerlingbegeleiding of de directie?</label>
                    <select name="problemHealthInternalConsult" id="problemHealthInternalConsult" data-load-source="{{select:url:short}}/{{url:part.module}}/options?o=problemHealthInternalConsult" data-load-value="id" data-load-label="name"></select>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="7" data-title="Inschrijven/Aanmelden">
            <fieldset class="form-fieldset">
                <legend>Inschrijving onder ontbindende voorwaarden</legend>

                <div class="row">
                    <div class="col-12" id="chbConditions" role="checkbox" data-type="checkbox" data-name="conditions" data-text="Inschrijving onder ontbindende voorwaarden?"></div>
                </div>
            </fieldset>

            <fieldset class="form-fieldset">
                <legend>Eventuele meldingen of opmerkingen</legend>

                <div class="row">
                    <label for="remarks" class="form-label">Meldingen of Opmerkingen</label>
                    <textarea name="remarks" id="remarks" rows="5" class="form-control"></textarea>
                </div>
            </fieldset>
        </div>

        <div class="card-body" data-step="8" data-title="Overzicht" id="overviewContainer"></div>

        <div class="card-footer btn-list">
            <button type="button" class="btn btn-primary me-auto d-none" id="btnPrevStep"><i class="icon ti ti-chevron-left"></i>Vorige stap</button>
            <button type="button" class="btn btn-primary ms-auto" id="btnNextStep">Volgende stap<i class="icon ti ti-chevron-right ms-2 me-n1"></i></button>
            <button type="button" class="btn btn-primary ms-auto d-none" id="btnOverview">Naar overzicht<i class="icon ti ti-chevron-right ms-2 me-n1"></i></button>
            <button type="submit" class="btn btn-primary ms-auto d-none" id="btnSubmit">Opslaan</button>
        </div>
    </form>
</div>