<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="from" class="form-label">Verstuur vanuit</label>
                    <select name="from" id="from" data-load-source="{{select:url:short}}/{{url:part.module}}/numbers" data-default-value="{{module:defaultNumber}}"></select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="groupId">Groep(en)</label>
                    <select name="groupId" id="groupId" data-load-source="{{select:url:short}}/{{url:part.module}}/groups" multiple data-search></select>
                </div>

                <div class="col-12" id="chbUseTemplate" role="checkbox" data-type="checkbox" data-name="useTemplate" data-text="Gebruik template" data-default-value="true" data-on-change="useTemplateView"></div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="template">Template</label>
                    <select name="template" id="template" data-load-source="{{select:url:short}}/{{url:part.module}}/templates" data-on-change="templateChange"></select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="content">Inhoud <span class="form-label-description" id="content_length"></span></label>
                    <input type="text" name="content" id="content" class="form-control" maxlength="160" required />
                </div>

                <div class="col-12" id="chbSendNow" role="checkbox" data-type="checkbox" data-name="sendNow" data-text="Verstuur direct" data-default-value="true" data-on-change="sendNowView"></div>
            </div>

            <div class="row" id="datetime_view">
                <div class="col-lg-9 mb-lg-3">
                    <label for="date" class="form-label">Datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="date" id="date" class="form-control" required />
                    </div>

                </div>

                <div class="col-lg-3 mb-lg-3">
                    <label for="time" class="form-label">Uur</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-clock"></i></span>
                        <input type="time" name="time" id="time" class="form-control" required />
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>