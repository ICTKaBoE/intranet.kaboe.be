<?php

use Core\Page;
use Security\User;
use Database\Repository\Tool;
use Helpers\Icon;

$tools = (new Tool)->get();
$user = User::get();

?>

<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-1">
                        <label for="<?= Page::id('sel'); ?>" class="form-label col-form-label">Kies tool</label>
                    </div>

                    <div class="col">
                        <select name="tool" id="<?= Page::id('sel'); ?>" class="form-select" data-render-item="item" data-render-option="option" data-on-change="toolOnChange">
                            <option value="0">--- Selecteer ---</option>
                            <?php foreach ($tools as $tool) :
                                if (!$tool->showInSettings) continue;
                            ?>
                                <option value="<?= $tool->id; ?>" <?php if ($tool->icon) : ?>data-icon="<?= str_replace("\"", "'", Icon::load($tool->icon)); ?>" <?php endif; ?>><?= $tool->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-responsive mb-0" id="<?= Page::id('tbl'); ?>" data-wrap="false" data-source="<?= Page::formAction("get"); ?>" data-entries="false" data-search="false" data-info="false" data-pagination="false"></table>
            </div>
        </div>
    </div>
</div>

<script>
    let toolSelectId = "<?= Page::id('sel'); ?>";
    let rightsSelectGroupId = "<?= Page::id('selgroup'); ?>";
    let rightsTableId = "<?= Page::id('tbl'); ?>";
</script>