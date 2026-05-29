<?php
const TEMPLATE_ROW = "
<div class='col-12 mb-3'>
    <div class='card'>
        <div class='card-header'>
            <img src='{{site:url}}/files/strategicDashboard/@guid@.png' alt='@name@' width='60'>
            <h1 class='card-title ms-3'>@name@</h1>
            <div class='card-actions btn-actions'>
                <button type='button' class='btn btn-icon btn-action' onclick='window.toggleBody(@id@)'><i class='icon ti ti-chevron-up' id='icon-@id@'></i></button>
            </div>
        </div>

        <div class='card-body' id='cb-@id@'>
            <div class='row row-cards row-deck'>@items@</div>
        </div>
    </div>
</div>";
?>

<div class="row row-cards row-deck" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}" data-template="<?= TEMPLATE_ROW; ?>" data-after-load-callback="loadNext"></div>