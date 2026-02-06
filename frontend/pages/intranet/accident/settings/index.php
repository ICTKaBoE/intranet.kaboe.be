<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="row" data-prefill>
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Details</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="format">Formaat</label>
                        <input type="text" name="format" id="format" class="form-control" required />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="policyNumber">Polisnummer</label>
                        <input type="text" class="form-control" id="policyNumber" name="policyNumber" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Verzekering</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="insurance.email">E-mail</label>
                        <input type="text" class="form-control" id="insurance.email" name="insurance.email" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Documenten</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="default.document.a" class="form-label">Deel A</label>
                        <select name="default.document.a" id="default.document.a" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" required></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="default.document.b" class="form-label">Deel B</label>
                        <select name="default.document.b" id="default.document.b" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" required></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="default.document.c" class="form-label">Deel C</label>
                        <select name="default.document.c" id="default.document.c" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" required></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Verzenden vanuit</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="send.from.name">Naam</label>
                        <input type="text" name="send.from.name" id="send.from.name" class="form-control" required />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="send.from.email">E-mail</label>
                        <input type="text" class="form-control" id="send.from.email" name="send.from.email" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Bericht naar aangever</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.creator.subject">Onderwerp</label>
                    <input type="text" name="mail.template.creator.subject" id="mail.template.creator.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.creator.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.creator.body" id="mail_template_creator_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Bericht naar alle ouders/contactpersonen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.parent.subject">Onderwerp</label>
                    <input type="text" name="mail.template.parent.subject" id="mail.template.parent.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.parent.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.parent.body" id="mail_template_parent_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Bericht naar verzekering</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.insurance.subject">Onderwerp</label>
                    <input type="text" name="mail.template.insurance.subject" id="mail.template.insurance.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.insurance.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.insurance.body" id="mail_template_insurance_body" class="form-control">
                </div>
            </div>
        </div>
    </div>
</form>