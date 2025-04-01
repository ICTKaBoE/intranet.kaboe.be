<?php
const TEMPLATE_TAB_CONTENT = "";
const TEMPLATE_ROW_CONTENT = "";

const TEMPLATE_NAVTAB = "<ul class='nav nav-tabs card-header-tabs' data-bs-toggle='tabs'>&navtabs&</ul>";
const TEMPLATE_CONTENT = "<div class='tab-content'>&contents&</div>";
?>

<div class="row row-cards">
    <div class="col-12">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card" data-prefill>
            <div class="card-header" role="list" id="lst{{page:id}}Navtab" data-source="{{list:url:full}}" data-template="<?= TEMPLATE_NAVTAB; ?>"></div>
            <div class="card-body" role="list" id="lst{{page:id}}Content" data-source="{{list:url:full}}" data-template="<?= TEMPLATE_CONTENT; ?>"></div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>
</div>