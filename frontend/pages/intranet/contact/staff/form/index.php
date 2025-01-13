<?php
const LIST_TEMPLATE = " <div class='datagrid-item'>
                            <div class='datagrid-item'>&title&</div>
                            <div class='datagrid-content'>&content&</div>
                        </div>";

const LIST_IMAGE = "    <img class='img-fluid rounded' src='{{site:url}}/frontend/shared/default/images/informat/employee/&informatGuid&.jpg' alt='&fullNameReversed&'>";
?>

<div class="row">
    <div class="col-12 col-md-9">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Info</h2>
            </div>

            <div class="card-body">
                <div class="datagrid" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_TEMPLATE; ?>"></div>
            </div>
        </div>
    </div>

    <div class="d-none d-md-block col-3">
        <div class="card" role="list" id="lst{{page:id}}Image" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= LIST_IMAGE; ?>"></div>
    </div>
</div>