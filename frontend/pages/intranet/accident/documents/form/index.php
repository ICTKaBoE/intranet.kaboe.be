<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="alias">Alias</label>
                    <input type="text" name="alias" id="alias" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="file" class="form-label">Bestand</label>
                    <input type="file" role="file" name="file" id="file" class="form-control" />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>