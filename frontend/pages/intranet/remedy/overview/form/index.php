<?php
const LIST_TEMPLATE = " <div class='datagrid-item mb-3'>
                            <div class='datagrid-item fw-bold'>#title#</div>
                            <div class='datagrid-content'>#content#</div>
                        </div>";
?>

<div class="row">
    <div class="col-12 col-lg-9">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title d-block">Ingeschreven leerlingen</h2>
            </div>

            <table role="table" id="tbl{{page:id}}Students" data-source="{{table:url:full}}Students/{{url:part.id}}" data-no-info data-no-paging></table>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Details</h2>
            </div>

            <div class="card-body" role="list" id="lst{{page:id}}Info" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE ?>"></div>
        </div>
    </div>
</div>