<div class="modal modal-blur fade show" id="modal-changePassword" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}ChangePassword" method="post" autocomplete="off" id="frm{{page:id}}ChangePassword" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Wachtwoord Wijzigen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div role="checkbox" data-type="checkbox" data-name="random" data-text="Random?" id="chbRandom"></div>

                    <div class="col">
                        <label class="form-label" for="password">Wachtwoord</label>
                        <input type="text" name="password" id="password" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-danger">Wijzigen</button>
            </div>
        </form>
    </div>
</div>