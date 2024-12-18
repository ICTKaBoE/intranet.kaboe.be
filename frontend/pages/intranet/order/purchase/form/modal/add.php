<div class="modal modal-blur fade show" id="modal-add" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Line" method="post" autocomplete="off" id="frm{{page:id}}Line" class="modal-content">
            <input type="hidden" name="purchaseId" id="purchaseId" value="{{url:part.id}}" />

            <div class="modal-header">
                <h5 class="modal-title">Lijn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-2 mb-lg-3 mb-3">
                        <label class="form-label" for="amount">Aantal</label>
                        <input type="number" step="1" min="0" name="amount" id="amount" class="form-control" required>
                        <div class="invalid-feedback" data-feedback-input="amount"></div>
                    </div>

                    <div class="col-lg-3 mb-lg-3 mb-3">
                        <label class="form-label" for="category">Categorie</label>
                        <select name="category" id="category" data-load-source="{{select:url:short}}/{{url:part.module}}/category" data-load-value="id" data-load-label="name" data-optgroup-attribute="optgroup" data-optgroup-value="id" data-optgroup-label="name" data-on-change="deviceView" data-render-item="renderOptgroupItem" required></select>
                        <div class="invalid-feedback" data-feedback-input="category"></div>
                    </div>

                    <div class="col-lg-7 mb-lg-3 mb-3">
                        <label class="form-label" for="assetId">Toestel</label>
                        <select name="assetId" id="assetId" data-load-source="[L@{{select:url:short}}/management/laptop;D@{{select:url:short}}/management/desktop;I@{{select:url:short}}/management/ipad;B@{{select:url:short}}/management/beamer;P@{{select:url:short}}/management/printer;F@{{select:url:short}}/management/firewall;S@{{select:url:short}}/management/switch;A@{{select:url:short}}/management/accesspoint]" data-load-value="id" data-load-label="[L@name;D@name;I@name;B@serialnumber;P@name;F@hostname;S@name;A@name]" data-search required></select>
                        <div class="invalid-feedback" data-feedback-input="assetId"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-lg-3 mb-3">
                        <label for="clarifycation" class="form-label">Verduidelijking</label>
                        <input type="text" name="clarifycation" id="clarifycation" class="form-control" />
                        <div class="invalid-feedback" data-feedback-input="clarifycation"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 mb-lg-3 mb-3">
                        <label for="quotePrice" class="form-label">Offerte prijs</label>
                        <input type="number" step="0.01" min="0" name="quotePrice" id="quotePrice" class="form-control" />
                        <div class="invalid-feedback" data-feedback-input="quotePrice"></div>
                    </div>

                    <div class="col mb-lg-3 mb-3 pt-5">
                        <label class="form-check">
                            <input type="checkbox" name="quoteVatIncluded" id="quoteVatIncluded" class="form-check-input">
                            <span class="form-check-label">btw. inbegrepen</span>
                        </label>
                        <div class="invalid-feedback" data-feedback-input="quoteVatIncluded"></div>
                    </div>

                    <div class="col mb-lg-3 mb-3 pt-5">
                        <label class="form-check">
                            <input type="checkbox" name="warrenty" id="warrenty" class="form-check-input">
                            <span class="form-check-label">Garantie?</span>
                        </label>
                        <div class="invalid-feedback" data-feedback-input="warrenty"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>
</div>