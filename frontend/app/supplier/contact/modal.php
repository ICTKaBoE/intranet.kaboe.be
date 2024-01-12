<div class="modal modal-blur fade" id="modal-contact" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
            <div class="modal-header">
                <h5 class="modal-title">Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" data-form-type="create|update">
                <div class="row mb-3">
                    <div class="col mb-lg-3">
                        <label class="form-label mb-1" for="supplierId">Leverancier</label>
                        <select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/{{url:part:module}}/overview" data-load-value="id" data-load-label="name"></select>
                        <div class="invalid-feedback" data-feedback-input="supplierId"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="name">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback" data-feedback-input="name"></div>
                    </div>

                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="firstName">Voornaam</label>
                        <input type="text" name="firstName" id="firstName" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="firstName"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="text" name="email" id="email" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="email"></div>
                    </div>

                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="phone">Telefoon</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="phone"></div>
                    </div>
                </div>
            </div>

            <div class="modal-body" data-form-type="delete">
                <input type="hidden" name="ids" id="ids" />
                <h1>Wenst u deze contacten te verwijderen?</h1>
            </div>

            <div class="modal-footer" data-form-type="create|update">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>

            <div class="modal-footer" data-form-type="delete">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
                <button type="submit" class="btn btn-danger">Ja</button>
            </div>
        </form>
    </div>
</div>