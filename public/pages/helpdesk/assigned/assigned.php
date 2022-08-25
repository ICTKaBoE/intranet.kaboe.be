<?php

use Core\Page;
?>

<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-responsive mb-0" data-click-to-edit="true" id="<?= Page::id('tbl'); ?>" data-wrap="false" data-source="<?= Page::formAction("get"); ?>" data-entries="false" data-search="false" data-info="false" data-pagination="false"></table>
            </div>
        </div>
    </div>
</div>