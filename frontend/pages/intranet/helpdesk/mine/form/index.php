<?php
const THREAD_TEMPLATE = "   <div class='card mb-3'>
								<div class='card-header card-header-light'>
									<h3 class='card-title'>
										@linked.creator.formatted.fullNameReversed@
										<span class='card-subtitle'>@formatted.creationDateTime@</span>
									</h3>
								</div>

								<div class='card-body'>@content@</div>
							</div>";

const ATTACHMENT_TEMPLATE = "   <div class='row mb-1'>
								    <div>@link@</div>
							    </div>";
?>

<?php if (\Router\Helpers::getId() === "add"): ?>
    <form action="{{form:url:full}}" enctype="multipart/form-data" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-on-change="deviceView" data-default-value="{{user:mainSchoolId}}" required></select>
                    <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                </div>

                <div class="col-lg-4 mb-3">
                    <label class="form-label" for="priority">Prioriteit</label>
                    <select name="priority" id="priority" data-load-source="{{select:url:short}}/{{url:part.module}}/priority" data-load-value="id" data-load-label="name" data-default-value="L" required></select>
                    <div class="invalid-feedback" data-feedback-input="priority"></div>
                </div>

                <div class="col-lg-4 mb-3">
                    <label class="form-label" for="category">Categorie</label>
                    <select name="category" id="category" data-load-source="{{select:url:short}}/{{url:part.module}}/category" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" required data-on-change="deviceView" data-render-item="renderOptgroupItem"></select>
                    <div class="invalid-feedback" data-feedback-input="category"></div>
                </div>

                <div class="col-lg-4 mb-3">
                    <label class="form-label" for="roomId">Lokaal</label>
                    <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/management/room" data-load-value="id" data-load-label="formatted.full" data-parent-select="schoolId" required></select>
                    <div class="invalid-feedback" data-feedback-input="roomId"></div>
                </div>

                <div class="col-lg-8 mb-3">
                    <label class="form-label" for="assetId">Toestel</label>
                    <select name="assetId" id="assetId" data-load-source="[L@{{select:url:short}}/management/laptop;D@{{select:url:short}}/management/desktop;I@{{select:url:short}}/management/ipad;B@{{select:url:short}}/management/beamer;P@{{select:url:short}}/management/printer;F@{{select:url:short}}/management/firewall;S@{{select:url:short}}/management/switch;A@{{select:url:short}}/management/accesspoint]" data-load-value="id" data-load-label="[L@name;D@name;I@name;B@serialnumber;P@name;F@hostname;S@name;A@name]" required></select>
                    <div class="invalid-feedback" data-feedback-input="assetId"></div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <label class="form-label" for="content">Beschrijving probleem</label>
                <input type="text" role="tinymce" name="content" id="content" class="form-control" required>
                <div class="invalid-feedback" data-feedback-input="content"></div>
            </div>

            <div class="row">
                <label class="form-label" for="attachments">Bijlagen</label>
                <input type="file" class="form-control" name="attachments" id="attachments" accept="image/*" multiple />
                <div class="invalid-feedback" data-feedback-input="attachments"></div>
            </div>
        </div>

        <div class="card-footer d-flex py-3">
            <button type="button" class="btn btn-link link-secondary ms-auto" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
<?php else: ?>
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row" data-prefill-id="{{url:part.id}}" data-locked-value="_lockedForm">
        <div class="col-12 col-lg-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title d-block">Reactie
                        <span class="card-subtitle d-block">
                            Het toevoegen van een reactie aan een gesloten ticket, zorgt ervoor dat het ticket automatisch terug opengezet wordt.
                        </span>
                    </h2>
                </div>

                <div class="card-body">
                    <input type="text" role="tinymce" name="content" id="content" class="form-control" data-no-lock required>
                    <div class="invalid-feedback" data-feedback-input="content"></div>
                </div>
            </div>

            <div role="list" id="lstThread{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/thread" data-template="<?= THREAD_TEMPLATE; ?>" data-extra="[ticketId={{url:part.id}}]"></div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Bestanden</h2>
                </div>

                <div class="card-body" role="list" id="lstAttachments{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/attachments" data-template="<?= ATTACHMENT_TEMPLATE; ?>" data-extra="[ticketId={{url:part.id}}]"></div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Details</h2>
                </div>

                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label mb-0" for="formatted.number">Nummer</label>
                        <input type="text" name="formatted.number" id="formatted.number" class="form-control" readonly>
                        <div class="invalid-feedback" data-feedback-input="formatted.number"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name"></select>
                        <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="priority">Prioriteit</label>
                        <select name="priority" id="priority" data-load-source="{{select:url:short}}/{{url:part.module}}/priority" data-load-value="id" data-load-label="name"></select>
                        <div class="invalid-feedback" data-feedback-input="priority"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="status">Status</label>
                        <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name"></select>
                        <div class="invalid-feedback" data-feedback-input="status"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="category">Categorie</label>
                        <select name="category" id="category" data-load-source="{{select:url:short}}/{{url:part.module}}/category" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-on-change="deviceView" data-render-item="renderOptgroupItem"></select>
                        <div class="invalid-feedback" data-feedback-input="category"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="assetId">Toestel</label>
                        <select name="assetId" id="assetId" data-load-source="[L@{{select:url:short}}/management/laptop;D@{{select:url:short}}/management/desktop;I@{{select:url:short}}/management/ipad;B@{{select:url:short}}/management/beamer;P@{{select:url:short}}/management/printer;F@{{select:url:short}}/management/firewall;S@{{select:url:short}}/management/switch;A@{{select:url:short}}/management/accesspoint]" data-load-value="id" data-load-label="[L@name;D@name;I@name;B@serialnumber;P@name;F@hostname;S@name;A@name]" required></select>
                        <div class="invalid-feedback" data-feedback-input="assetId"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="roomId">Lokaal</label>
                        <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/management/room" data-load-value="id" data-load-label="formatted.full" data-parent-select="schoolId" data-search required></select>
                        <div class="invalid-feedback" data-feedback-input="roomId"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="creatorUserId">Aangemaakt door</label>
                        <select name="creatorUserId" id="creatorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" disabled></select>
                        <div class="invalid-feedback" data-feedback-input="creatorUserId"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1" for="assignedToUserId">Toegewezen aan</label>
                        <select name="assignedToUserId" id="assignedToUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" data-search></select>
                        <div class="invalid-feedback" data-feedback-input="assignedToUserId"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<script>
    let add = "<?= (\Router\Helpers::getId() === "add"); ?>";
</script>