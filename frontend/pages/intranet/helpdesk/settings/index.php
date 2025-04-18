<form action="{{form:url:full}}" class="row" method="post" id="frm{{page:id}}" data-prefill>
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Algemeen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="lastNumber">Laatste nummer</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="lastNumber" id="lastNumber">
                        <button class="btn" type="button" id="btnResetLastNumber">Reset</button>
                    </div>

                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="format">Formaat</label>
                    <input type="text" name="format" id="format" class="form-control" required />

                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="assignableUsers">Kan toegewezen worden aan</label>
                    <select name="assignableUsers" id="assignableUsers" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" multiple data-search></select>

                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Antwoorden</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.reply.name">Naam</label>
                    <input type="text" name="mail.reply.name" id="mail.reply.name" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.reply.email">E-mailadres</label>
                    <input type="text" name="mail.reply.email" id="mail.reply.email" class="form-control" />

                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Nieuw</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.new.subject">Onderwerp</label>
                    <input type="text" name="mail.template.new.subject" id="mail.template.new.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.new.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.new.body" id="mail_template_new_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateNewReply" role="checkbox" data-type="checkbox" data-name="mail.template.new.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Update aan maker</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.update.subject">Onderwerp</label>
                    <input type="text" name="mail.template.update.subject" id="mail.template.update.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.update.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.update.body" id="mail_template_update_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateUpdateReply" role="checkbox" data-type="checkbox" data-name="mail.template.update.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Toegewezen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.assigned.subject">Onderwerp</label>
                    <input type="text" name="mail.template.assigned.subject" id="mail.template.assigned.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.assigned.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.assigned.body" id="mail_template_assigned_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateAssignedReply" role="checkbox" data-type="checkbox" data-name="mail.template.assigned.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Update aan toegewezene</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.assignedUpdate.subject">Onderwerp</label>
                    <input type="text" name="mail.template.assignedUpdate.subject" id="mail.template.assignedUpdate.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.assignedUpdate.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.assignedUpdate.body" id="mail_template_assignedUpdate_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateAssignedUpdateReply" role="checkbox" data-type="checkbox" data-name="mail.template.assignedUpdate.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>
    </div>
</form>