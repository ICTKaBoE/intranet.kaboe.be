<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="card col-12 col-lg-4 mx-auto" data-prefill>
    <div class="card-body">
        <div class="col-12 mb-3">
            <label class="form-label" for="description">Uitleg</label>
            <textarea type="text" name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>
    </div>

    <div class="card-body">
        <div class="col-12 mb-3">
            <label for="mail.to.groupIds" class="form-label">Melding sturen naar</label>
            <select name="mail.to.groupIds" id="mail.to.groupIds" data-load-source="{{select:url:short}}/configuration/groups" multiple></select>
        </div>

        <div class="col-12 mb-3">
            <label class="form-label" for="mail.template.new.subject">Onderwerp</label>
            <input type="text" name="mail.template.new.subject" id="mail.template.new.subject" class="form-control" />
        </div>

        <div class="col-12 mb-3">
            <label for="mail.template.new.body" class="form-label">Body</label>
            <input type="text" role="tinymce" name="mail.template.new.body" id="mail_template_new_body" class="form-control">
        </div>
    </div>
</form>