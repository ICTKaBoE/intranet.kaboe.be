<?php
const LIST_TEMPLATE = " <div class='datagrid-item'>
                            <div class='datagrid-title'><strong>#title#</strong></div>
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
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}/{{url:part.id}}?co=0" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 1</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co1" data-source="{{list:url:full}}/{{url:part.id}}?co=1" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 2</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co2" data-source="{{list:url:full}}/{{url:part.id}}?co=2" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 3</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co3" data-source="{{list:url:full}}/{{url:part.id}}?co=3" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 4</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co4" data-source="{{list:url:full}}/{{url:part.id}}?co=4" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 5</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co5" data-source="{{list:url:full}}/{{url:part.id}}?co=5" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Co-account 6</h2>
            </div>

            <div class="card-body">
                <div class="datagrid datagrid-horizontal" style="--tblr-datagrid-padding: 0.25rem" role="list" id="lst{{page:id}}Co6" data-source="{{list:url:full}}/{{url:part.id}}?co=6" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Geschiedenis binnen COLTD vzw (Nieuw - Oud)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}History" data-source="{{list:url:full}}/{{url:part.id}}?what=history" data-template="<?= LIST_TEMPLATE_HISTORY ?>"></div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Adres(sen)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Address" data-source="{{list:url:full}}/{{url:part.id}}?what=address" data-template="<?= LIST_TEMPLATE_ADDRESS ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Relatie(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Relation" data-source="{{list:url:full}}/{{url:part.id}}?what=relation" data-template="<?= LIST_TEMPLATE_RELATION ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Telefoon/GSM</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Number" data-source="{{list:url:full}}/{{url:part.id}}?what=number" data-template="<?= LIST_TEMPLATE_NUMBER ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Email(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Email" data-source="{{list:url:full}}/{{url:part.id}}?what=email" data-template="<?= LIST_TEMPLATE_EMAIL ?>"></div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Bankgegeven(s)</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Bank" data-source="{{list:url:full}}/{{url:part.id}}?what=bank" data-template="<?= LIST_TEMPLATE_BANK ?>"></div>
        </div>
    </div>

    <div class="d-none d-lg-block col-3">
        <div class="card" role="list" id="lst{{page:id}}Image" data-source="{{list:url:full}}/{{url:part.id}}?what=image" data-template="<?= LIST_IMAGE; ?>"></div>
    </div>
</div>