<?php
const TEMPLATE_REPEAT_ADDRESS = '   <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-content">{{address:formatted.full}}</div>
                                        </div>
                                    </div>';

const TEMPLATE_REPEAT_RELATION = '  <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-content">{{relation:formatted.typeWithFullNameReversed}}</div>
                                        </div>
                                    </div>';

const TEMPLATE_REPEAT_NUMBER = '    <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-content">{{number:formatted.typeWithNumberLink}}</div>
                                        </div>
                                    </div>';

const TEMPLATE_REPEAT_EMAIL = ' <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-content">{{email:formatted.typeWithEmailLink}}</div>
                                    </div>
                                </div>';

const TEMPLATE_REPEAT_BANK = '  <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-content">{{bank:formatted.details}}</div>
                                    </div>
                                </div>';

const TEMPLATE_REPEAT_HISTORY = '   <div class="datagrid">
                                        <div class="datagrid-item">
                                            <div class="datagrid-content">{{history:formatted.details}}</div>
                                        </div>
                                    </div>';
?>

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Info</h2>
            </div>

            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Naam</div>
                        <div class="datagrid-content">{{student:name}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Voornaam</div>
                        <div class="datagrid-content">{{student:firstName}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Geboortedatum</div>
                        <div class="datagrid-content">{{student:formatted.birthDate}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Geschiedenis (Nieuw - Oud)</h2>
            </div>

            <div class="card-body">
                {{student:repeat.history}}
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Adres(sen)</h2>
            </div>

            <div class="card-body">
                {{student:repeat.address}}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Relatie(s)</h2>
            </div>

            <div class="card-body">
                {{student:repeat.relation}}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Telefoon/GSM</h2>
            </div>

            <div class="card-body">
                {{student:repeat.number}}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Email(s)</h2>
            </div>

            <div class="card-body">
                {{student:repeat.email}}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Bankgegeven(s)</h2>
            </div>

            <div class="card-body">
                {{student:repeat.bank}}
            </div>
        </div>
    </div>

    <div class="d-none d-lg-block col-3">
        <div class="card">
            <img class="img-fluid rounded" src="{{site:url}}/frontend/shared/default/images/informat/student/{{student:informatGuid}}.jpg" alt="{{student:formatted.fullNameReversed}}">
        </div>
    </div>
</div>