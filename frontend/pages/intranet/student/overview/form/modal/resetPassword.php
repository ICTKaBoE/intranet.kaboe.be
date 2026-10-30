<div class="modal modal-blur fade" id="modal-resetPassword" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}ResetPasswordSingle/{{url:part.id}}" method="post" autocomplete="off" id="frm{{page:id}}ResetPasswordSingle" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Wachtwoord resetten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12" id="chbMain" role="checkbox" data-type="checkbox" data-name="main" data-text="Hoofdaccount" data-on-change="resetPasswordMain"></div>
                    <div class="col-11 offset-1 d-none" id="chbMainForceChangePasswordNextLogin" role="checkbox" data-type="checkbox" data-name="mainForceChangePasswordNextLogin" data-text="Forceer nieuw wachtwoord instellen bij volgende aanmelding"></div>
                    <div class="col-11 offset-1 d-none" id="chbMainPrintDocument" role="checkbox" data-type="checkbox" data-name="mainPrintDocument" data-text="Document afdrukken" data-default-value="true"></div>
                    <div class="col-12" id="chbCo1" role="checkbox" data-type="checkbox" data-name="co1" data-text="Co-account 1"></div>
                    <div class="col-12" id="chbCo2" role="checkbox" data-type="checkbox" data-name="co2" data-text="Co-account 2"></div>
                    <div class="col-12" id="chbCo3" role="checkbox" data-type="checkbox" data-name="co3" data-text="Co-account 3"></div>
                    <div class="col-12" id="chbCo4" role="checkbox" data-type="checkbox" data-name="co4" data-text="Co-account 4"></div>
                    <div class="col-12" id="chbCo5" role="checkbox" data-type="checkbox" data-name="co5" data-text="Co-account 5"></div>
                    <div class="col-12" id="chbCo6" role="checkbox" data-type="checkbox" data-name="co6" data-text="Co-account 6"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-danger">Reset</button>
            </div>
        </form>
    </div>
</div>