<div class="modal modal-blur fade show" id="modal-add" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Item" method="post" autocomplete="off" id="frm{{page:id}}Item" class="modal-content">
            <input type="hidden" name="playlistId" id="playlistId" value="{{url:part.id}}" />

            <div class="modal-header">
                <h5 class="modal-title">Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="mediaId">Media</label>
                        <select name="mediaId" id="mediaId" data-load-source="{{select:url:short}}/{{url:part.module}}/media" data-load-value="id" data-load-label="alias" required></select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label" for="duration">Duur (seconden, bij video wordt deze waarde overschreven)</label>
                        <input type="number" name="duration" id="duration" class="form-control" />
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