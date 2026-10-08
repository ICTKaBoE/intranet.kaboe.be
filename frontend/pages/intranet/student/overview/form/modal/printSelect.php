<div class="modal modal-blur fade" id="modal-printSelect" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}Print/{{url:part.id}}" method="post" autocomplete="off" id="frm{{page:id}}PrintSelect" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Printen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="what">Wat?</label>
                        <select name="what" id="what">
                            <option value="cover">Voorblad Rapport</option>
                            <option value="card">Studentenkaart</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-success">Printen</button>
            </div>
        </form>
    </div>
</div>