<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/" data-load-value="id" data-load-label="name" required></select>
                    <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="buildingId">Gebouw</label>
                    <select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/{{url:part.module}}/building/" data-load-value="id" data-load-label="name" required data-parent-select="schoolId"></select>
                    <div class="invalid-feedback" data-feedback-input="buildingId"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="roomId">Lokaal</label>
                    <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/{{url:part.module}}/room/" data-load-value="id" data-load-label="formatted.name" required data-parent-select="buildingId"></select>
                    <div class="invalid-feedback" data-feedback-input="roomId"></div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="cabinetId">Netwerkkast</label>
                    <select name="cabinetId" id="cabinetId" data-load-source="{{select:url:short}}/{{url:part.module}}/cabinet/" data-load-value="id" data-load-label="name" required data-parent-select="roomId"></select>
                    <div class="invalid-feedback" data-feedback-input="cabinetId"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="name"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="manufacturer">Merk</label>
                    <input type="text" name="manufacturer" id="manufacturer" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="manufacturer"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="model">Model</label>
                    <input type="text" name="model" id="model" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="model"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="serialnumber">Serienummer</label>
                    <input type="text" name="serialnumber" id="serialnumber" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="serialnumber"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="macaddress">MAC Adres</label>
                    <input type="text" name="macaddress" id="macaddress" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="macaddress"></div>
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label" for="ports"># Poorten</label>
                    <input type="number" name="ports" id="ports" class="form-control" step="1" min="0" max="48" required />
                    <div class="invalid-feedback" data-feedback-input="ports"></div>
                </div>

                <div class="col-12 col-lg-2 mb-3">
                    <label class="form-label" for="ip">IP Adres</label>
                    <input type="text" name="ip" id="ip" class="form-control" required />
                    <div class="invalid-feedback" data-feedback-input="ip"></div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>