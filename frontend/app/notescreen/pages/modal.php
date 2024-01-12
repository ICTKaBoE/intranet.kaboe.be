<div class="modal modal-blur fade" id="modal-notescreen-pages" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
            <div class="modal-header">
                <h5 class="modal-title">Pagina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" data-form-type="create|update">
                <input type="hidden" name="schoolId" id="schoolId" />
                <div class="mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                    <div class="invalid-feedback" data-feedback-input="name"></div>
                </div>
            </div>

            <div class="modal-body" data-form-type="delete">
                <input type="hidden" name="ids" id="ids" />
                <h1>Wenst u deze pagina's te verwijderen?</h1>
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