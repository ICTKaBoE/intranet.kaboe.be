<form action="{{form:url:full}}extranet-request" method="post" class="card col-md-3 m-auto">
    <div class="card-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label for="filenumber" class="form-label">Uw dossier-nummer</label>
                <input type="text" name="filenumber" id="filenumber" class="form-control" data-mask="0000-000000" required />
            </div>

            <div class="col-12 mb-3">
                <label for="insz" class="form-label">Rijksregisternummer van het kind</label>
                <input type="text" name="insz" id="insz" class="form-control" required />
            </div>
        </div>
    </div>

    <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary">Dossier openen</button>
    </div>
</form>