<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" data-mask="0000-0000" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label for="visibleFrom" class="form-label">Zichtbaar vanaf</label>
                    <input type="text" role="datepicker" name="visibleFrom" id="visibleFrom" class="form-control" required />
                </div>
                <div class="col-lg-6 col-12 mb-3">
                    <label for="visibleUntil" class="form-label">Zichtbaar tot</label>
                    <input type="text" role="datepicker" name="visibleUntil" id="visibleUntil" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>