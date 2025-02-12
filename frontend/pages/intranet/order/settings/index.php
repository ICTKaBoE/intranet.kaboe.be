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
                    <label class="form-label" for="acceptableUsers">Kan goedgekeurd worden door</label>
                    <select name="acceptableUsers" id="acceptableUsers" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" multiple data-search></select>

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
                <h2 class="card-title">Berichten - Offerte aanvragen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.quote.subject">Onderwerp</label>
                    <input type="text" name="mail.template.quote.subject" id="mail.template.quote.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.quote.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.quote.body" id="mail_template_quote_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateQuoteReploy" role="checkbox" data-type="checkbox" data-name="mail.template.quote.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Bestelling plaatsen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.order.subject">Onderwerp</label>
                    <input type="text" name="mail.template.order.subject" id="mail.template.order.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.order.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.order.body" id="mail_template_order_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateOrderReploy" role="checkbox" data-type="checkbox" data-name="mail.template.order.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Wachtend op goedkeuring</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.accept.subject">Onderwerp</label>
                    <input type="text" name="mail.template.accept.subject" id="mail.template.accept.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.accept.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.accept.body" id="mail_template_accept_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateAcceptReploy" role="checkbox" data-type="checkbox" data-name="mail.template.accept.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Goed-/Afgekeurd</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.status.subject">Onderwerp</label>
                    <input type="text" name="mail.template.status.subject" id="mail.template.status.subject" class="form-control" />

                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.status.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.status.body" id="mail_template_status_body" class="form-control">

                </div>

                <div class="col-12 mb-3" id="chbMailTemplateStatusReploy" role="checkbox" data-type="checkbox" data-name="mail.template.status.reply" data-text="Mag antwoorden op de mail?"></div>
            </div>
        </div>
    </div>
</form>