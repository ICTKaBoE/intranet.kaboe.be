<div class="card col-12 col-lg-4 mx-auto">
    <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" data-prefill>
        <div class="card-body">
            <div class="col-12 mb-3">
                <label class="form-label" for="lastNumber">Laatste nummer</label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="lastNumber" id="lastNumber">
                    <button class="btn" type="button" id="btnResetLastNumber">Reset</button>
                </div>
                <div class="invalid-feedback" data-feedback-input="lastNumber"></div>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label" for="format">Formaat</label>
                <input type="text" name="format" id="format" class="form-control" required />
                <div class="invalid-feedback" data-feedback-input="format"></div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>