<?php

use Security\Request;
use Database\Repository\Helpdesk;
use Database\Repository\Tool;
use Database\Repository\ToolPermission;
use Security\Session;

$helpdesk = (new Helpdesk)->get(Request::parameter(REQUEST_ROUTE_PARAMETER_ID))[0];
$tool = (new Tool)->getByRoute(Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL));
$toolPermission = (new ToolPermission)->getByToolIdAndUpn($tool->id, Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
?>

<div class="row row-cards">
    <div class="col-9">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Uw reactie</h3>
            </div>

            <form id="<?= Core\Page::id("frm", "react"); ?>" action="<?= Core\Page::formAction("POST", "react"); ?>" method="POST" data-after-submit="submitReactForm">
                <input type="hidden" name="helpdeskId" value="<?= $helpdesk->id; ?>">
                <div class="card-body">
                    <textarea name="message" id="message" class="form-control" rows="6" data-bs-toggle="autosize" required></textarea>
                </div>

                <div class="card-footer">
                    <div class="d-flex">
                        <button type="submit" role="button" class="ms-auto btn btn-success">Reageren</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reacties</h3>
            </div>

            <div role="list" id="<?= Core\Page::id('lst', 'messages'); ?>" data-source="<?= Core\Page::formAction('GET', 'messages'); ?>">
                <div role="template">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <strong class="col-9 text-bold" data-prefill="name"></strong>
                            <div class="col-3 text-end" data-prefill="age"></div>
                        </div>

                        <div class="row">
                            <div class="col" data-prefill="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Info</h3>
            </div>
            <form class="card-body" id="<?= Core\Page::id("frm", "info"); ?>" action="<?= Core\Page::formAction("POST", "info"); ?>" method="POST">
                <input type="hidden" name="helpdeskId" value="<?= $helpdesk->id; ?>">
                <div class="form-group">
                    <label class="form-label">Onderwerp</label>
                    <p><?= $helpdesk->subject; ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Aangemaakt door</label>
                    <p><?= $helpdesk->creatorName; ?></p>
                </div>

                <div class="form-group mb-3">
                    <label for="assignedToUpn" class="form-label">Toegewezen aan</label>
                    <?php if ($toolPermission->react == 1) : ?>
                        <select class="form-select" name="assignedToUpn" id="assignedToUpn" data-on-change="submitInfoForm" data-load-source="<?= Core\Page::formAction('GET', 'assignToMembers'); ?>" data-load-value="mail" data-load-label="displayName" data-default-value="<?= $helpdesk->assignedToUpn; ?>"></select>
                    <?php else : ?>
                        <p><?= $helpdesk->assignedToName ?? '/'; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="priority" class="form-label">Prioriteit</label>
                    <?php if ($toolPermission->react == 1) : ?>
                        <select class="form-select" name="priority" id="priority" data-on-change="submitInfoForm">
                            <?php foreach (Core\Config::get("tool/helpdesk/priority") as $idx => $priority) : ?>
                                <option value="<?= $idx; ?>" <?php if ($helpdesk->priority === $idx) : ?>selected<?php endif; ?>><?= $priority['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else : ?>
                        <p><?= Core\Config::get("tool/helpdesk/priority/{$helpdesk->priority}")['name']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Laatste update</label>
                    <p><?= $helpdesk->age; ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">School</label>
                    <p><?= $helpdesk->schoolName; ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <p><?= $helpdesk->typeName; ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Toestel</label>
                    <p><?= $helpdesk->deviceName; ?></p>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historiek</h3>
            </div>

            <div role="list" id="<?= Core\Page::id('lst', 'history'); ?>" data-source="<?= Core\Page::formAction('GET', 'history'); ?>">
                <div role="template">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" data-prefill="typeIconHtml"></span>
                            </div>
                            <div class="col text-truncate">
                                <div class="text-reset d-block" data-prefill="info"></div>
                                <div class="d-block text-muted text-truncate mt-m1" data-prefill="timestampFormatted"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let reactFormId = '<?= Core\Page::id("frm", "react"); ?>';
    let infoFormId = '<?= Core\Page::id("frm", "info"); ?>';
    let historyListId = '<?= Core\Page::id('lst', 'history'); ?>';
    let messagesListId = '<?= Core\Page::id('lst', 'messages'); ?>';
</script>