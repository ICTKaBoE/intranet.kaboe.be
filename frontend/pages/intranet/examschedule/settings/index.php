<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" data-prefill>
    <div class="card col-12 col-lg-4 mx-auto">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="slot.duration">Registratie per (minuten)</label>
                    <input role="text" class="form-control" id="slot.duration" name="slot.duration" data-mask="00:00:00" required>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="slot.min">Registratie vanaf</label>
                    <input role="text" class="form-control" id="slot.min" name="slot.min" data-mask="00:00:00" required>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="slot.max">Registratie tot en met</label>
                    <input role="text" class="form-control" id="slot.max" name="slot.max" data-mask="00:00:00" required>
                </div>
            </div>
        </div>
    </div>
</form>