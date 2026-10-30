<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="row" data-prefill>
    <div class="col-12 col-lg-4">
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

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Documenten</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="default.document.mainResetPassword" class="form-label">Wachtwoord reset hoofdaccount</label>
                        <select name="default.document.mainResetPassword" id="default.document.mainResetPassword" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" data-label="alias" required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="default.document.reportCover" class="form-label">Voorblad Rapport</label>
                        <select name="default.document.reportCover" id="default.document.reportCover" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" data-label="alias" required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="default.document.card" class="form-label">Leerlingenkaart</label>
                        <select name="default.document.card" id="default.document.card" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" data-label="alias" required></select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Bericht naar co-account</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.coaccount.subject">Onderwerp</label>
                    <input type="text" name="mail.template.coaccount.subject" id="mail.template.coaccount.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.coaccount.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.coaccount.body" id="mail_template_coaccount_body" class="form-control">
                </div>
            </div>
        </div>
    </div>
</form>