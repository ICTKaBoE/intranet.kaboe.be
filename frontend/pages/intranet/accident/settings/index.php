<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" data-prefill enctype="multipart/form-data">
    <div class="card col-12 col-lg-6 mx-auto">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="lastNumber">Laatste nummer</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="lastNumber" id="lastNumber">
                        <button class="btn" type="button" id="btnResetLastNumber">Reset</button>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="format">Formaat</label>
                    <input type="text" name="format" id="format" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="policyNumber">Polisnummer</label>
                    <input type="text" class="form-control" id="policyNumber" name="policyNumber" required>
                </div>

                <div class="col-12 mb-3">
                    <label for="default.document" class="form-label">Standaard document</label>
                    <select name="default.document" id="default.document" data-load-source="{{select:url:short}}/{{url:part.module}}/documents/" data-load-value="id" data-load-label="name" required></select>
                </div>
            </div>
        </div>
    </div>
</form>