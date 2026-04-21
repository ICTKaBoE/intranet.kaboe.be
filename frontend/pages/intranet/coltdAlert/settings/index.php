<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill>
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="numbers">Nummers</label>
                    <textarea name="numbers" id="numbers" rows="5" class="form-control"></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="defaultNumber">Standaard nummer</label>
                    <input type="number" name="defaultNumber" id="defaultNumber" class="form-control">
                </div>
            </div>
        </div>
    </form>
</div>