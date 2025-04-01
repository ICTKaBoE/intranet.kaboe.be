<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-lg-10 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label" for="color">Kleur</label>
                    <input type="color" name="color" id="color" class="form-control" />
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <label for="syncUpdateMail" class="form-label">Update ontvangen over Sync</label>
                    <textarea name="syncUpdateMail" id="syncUpdateMail" class="form-control" rows="5"></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 mb-3">
                    <label class="form-label" for="intuneOrderIdPrefix">Intune Order ID Prefix</label>
                    <input type="text" name="intuneOrderIdPrefix" id="intuneOrderIdPrefix" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="jamfIpadPrefix">JAMF iPad Prefix</label>
                    <input type="text" name="jamfIpadPrefix" id="jamfIpadPrefix" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="adJobTitlePrefix">AD - Job Title Prefix</label>
                    <input type="text" name="adJobTitlePrefix" id="adJobTitlePrefix" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="adOuPart">AD - OU Part</label>
                    <input type="text" name="adOuPart" id="adOuPart" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="adSecGroupPart">AD - Security Group Part</label>
                    <input type="text" name="adSecGroupPart" id="adSecGroupPart" class="form-control" />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>