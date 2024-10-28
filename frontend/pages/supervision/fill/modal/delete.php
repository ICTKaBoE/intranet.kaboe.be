<div class="modal modal-blur fade" id="modal-delete" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="delete" id="frm{{page:id}}Delete" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verwijderen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="ids" id="ids" />
                <h1>Wenst u deze middagtoezicht te verwijderen?</h1>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
                <button type="submit" class="btn btn-danger">Ja</button>
            </div>
        </form>
    </div>
</div>