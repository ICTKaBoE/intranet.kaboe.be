<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" enctype="multipart/form-data method=" post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label" for="order">Volgorde</label>
                    <input type="number" name="order" id="order" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="image">Icoon</label>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*" multiple />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>