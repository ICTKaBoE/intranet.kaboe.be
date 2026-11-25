<div class="modal modal-blur fade" id="modal-upload" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Upload/{{url:part.id}}" method="post" autocomplete="off" id="frm{{page:id}}Upload" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bestanden uploaden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="documentB" class="form-label">Geneeskundig getuigschrift (gedeelte B)</label>
                        <input type="file" name="documentB" id="documentB" class="form-control" accept=".pdf" />
                    </div>

                    <div class="col-12 mb-3">
                        <label for="documentC" class="form-label">Informatieblad (gedeelte C)</label>
                        <input type="file" name="documentC" id="documentC" class="form-control" accept=".pdf" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-success">Uploaden</button>
            </div>
        </form>
    </div>
</div>