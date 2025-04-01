<div class="modal modal-blur fade show" id="modal-requestQuote" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}RequestQuote" method="post" autocomplete="off" id="frm{{page:id}}RequestQuote" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Offerte Aanvragen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="ids" id="ids" />
                <h1>Bent u zeker dat u voor de geselecteerde items een offerte wilt aanvragen?</h1>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Ja</button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
            </div>
        </form>
    </div>
</div>