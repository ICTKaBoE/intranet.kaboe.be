<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="name"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="contactName">Contactpersoon</label>
                    <input type="text" name="contactName" id="contactName" class="form-control" />
                    <div class="invalid-feedback" data-feedback-input="contactName"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="email"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="phone">Telefoon</label>
                    <input type="phone" name="phone" id="phone" class="form-control" />
                    <div class="invalid-feedback" data-feedback-input="phone"></div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>