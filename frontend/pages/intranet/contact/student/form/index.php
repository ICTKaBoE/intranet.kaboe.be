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

const LIST_TEMPLATE = " <div class='datagrid-item'>
                            <div class='datagrid-item'>#title#</div>
                            <div class='datagrid-content'>#content#</div>
                        </div>";

const LIST_TEMPLATE_ADDRESS = " <div>#address#</div>";
const LIST_TEMPLATE_RELATION = "<div>#relation#</div>";
const LIST_TEMPLATE_NUMBER = "  <div>#number#</div>";
const LIST_TEMPLATE_EMAIL = "  <div>#email#</div>";
const LIST_TEMPLATE_BANK = "<div>#bank#</div>";
const LIST_TEMPLATE_HISTORY = " <div>#history#</div>";
const LIST_IMAGE = "<img class='img-fluid rounded' src='https://kaboe.be/frontend/shared/default/images/informat/student/#informatGuid#.jpg' alt='#fullNameReversed#'>";
?>

<div class="row">
    <div class="col-12 col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Info</h2>
            </div>

            <div class="card-body">
                <div class="datagrid" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Geschiedenis (Nieuw - Oud)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}History" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_HISTORY ?>"></div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Adres(sen)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Address" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_ADDRESS ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Relatie(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Relation" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_RELATION ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Telefoon/GSM</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Number" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_NUMBER ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Email(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Email" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_EMAIL ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Bankgegeven(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Bank" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE_BANK ?>"></div>
        </div>
    </div>

    <div class="d-none d-lg-block col-3">
        <div class="card" role="list" id="lst{{page:id}}Image" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_IMAGE; ?>"></div>
    </div>
</div>