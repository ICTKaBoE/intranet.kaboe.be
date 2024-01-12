<div class="modal modal-blur fade" id="modal-notescreen-articles" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
            <div class="modal-header">
                <h5 class="modal-title">Artikel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" data-form-type="create|update">
                <input type="hidden" name="schoolId" id="schoolId" />
                <div class="mb-3">
                    <label class="form-label" for="title">Titel</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                    <div class="invalid-feedback" data-feedback-input="title"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="notescreenPageId">Pagina</label>
                    <select name="notescreenPageId" id="notescreenPageId" data-load-source="{{select:url:short}}/{{url:part:module}}/pages" data-load-value="id" data-load-label="name" required></select>
                    <div class="invalid-feedback" data-feedback-input="notescreenPageId"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="content">Inhoud</label>
                    <textarea role="tinymce" name="content" id="content"></textarea>
                    <div class="invalid-feedback" data-feedback-input="content"></div>
                </div>

                <div class="">
                    <label class="form-label" for="displayTime">Toon tijd (milliseconden)</label>
                    <input type="number" name="displayTime" id="displayTime" class="form-control" required min="0" max="60000">
                    <div class="invalid-feedback" data-feedback-input="displayTime"></div>
                </div>
            </div>

            <div class="modal-body" data-form-type="delete">
                <input type="hidden" name="ids" id="ids" />
                <h1>Wenst u deze artikelen te verwijderen?</h1>
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