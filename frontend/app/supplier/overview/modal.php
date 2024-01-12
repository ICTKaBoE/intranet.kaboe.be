<div class="modal modal-blur fade" id="modal-supplier" tabindex="-1" style="display: none;" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="post" id="frm{{page:id}}" class="modal-content" data-action-field="faction">
            <div class="modal-header">
                <h5 class="modal-title">Leverancier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" data-form-type="create|update">
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="name">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback" data-feedback-input="name"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="text" name="email" id="email" class="form-control" required>
                        <div class="invalid-feedback" data-feedback-input="email"></div>
                    </div>

                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="phone">Telefoon</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="phone"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="street">Straat</label>
                        <input type="text" name="street" id="street" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="street"></div>
                    </div>

                    <div class="col-lg-3 mb-lg-3">
                        <label class="form-label" for="number">Nummer</label>
                        <input type="text" name="number" id="number" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="number"></div>
                    </div>

                    <div class="col-lg-3 mb-lg-3">
                        <label class="form-label" for="bus">Bus</label>
                        <input type="text" name="bus" id="bus" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="bus"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-3 mb-lg-3">
                        <label class="form-label" for="zipcode">Postcode</label>
                        <input type="text" name="zipcode" id="zipcode" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="zipcode"></div>
                    </div>

                    <div class="col-lg-6 mb-lg-3">
                        <label class="form-label" for="city">Stad</label>
                        <input type="text" name="city" id="city" class="form-control">
                        <div class="invalid-feedback" data-feedback-input="city"></div>
                    </div>

                    <div class="col-lg-3 mb-lg-3">
                        <label class="form-label" for="country">Land</label>
                        <select name="country" id="country">
                            <option value="België">België</option>
                            <option value="Nederland">Nederland</option>
                        </select>
                        <div class="invalid-feedback" data-feedback-input="country"></div>
                    </div>
                </div>
            </div>

            <div class="modal-body" data-form-type="delete">
                <input type="hidden" name="ids" id="ids" />
                <h1>Wenst u deze leveranciers te verwijderen?</h1>
            </div>

            <div class="modal-footer" data-form-type="create|update">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>

            <div class="modal-footer" data-form-type="delete">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Nee</button>
                <button type="submit" class="btn btn-danger">Ja</button>
            </div>
        </form>
    </div>
</div>