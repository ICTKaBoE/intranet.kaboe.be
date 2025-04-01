<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" data-prefill enctype="multipart/form-data">
    <div class="card col-12 col-lg-6 mx-auto">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="policyNumber">Polisnummer</label>
                    <input type="text" class="form-control" id="policyNumber" name="policyNumber" required>
                </div>

                <div class="col-12 mb-3">
                    <label for="blancoForm.original" class="form-label">Aangifteformulier</label>
                    <input type="file" role="file" name="blancoForm.original" id="blancoForm_original" class="form-control" required>
                </div>
            </div>
        </div>
    </div>
</form>