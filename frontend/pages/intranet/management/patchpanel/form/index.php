<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/" data-load-value="id" data-load-label="name" required></select>

                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="buildingId">Gebouw</label>
                    <select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/{{url:part.module}}/building/" data-load-value="id" data-load-label="name" required data-parent-select="schoolId"></select>

                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="roomId">Lokaal</label>
                    <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/{{url:part.module}}/room/" data-load-value="id" data-load-label="formatted.name" required data-parent-select="buildingId"></select>

                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="cabinetId">Netwerkkast</label>
                    <select name="cabinetId" id="cabinetId" data-load-source="{{select:url:short}}/{{url:part.module}}/cabinet/" data-load-value="id" data-load-label="name" required data-parent-select="roomId"></select>

                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-9 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />

                </div>

                <div class="col-12 col-lg-3 mb-3">
                    <label class="form-label" for="patchpoints">Aantal patchpunten</label>
                    <input type="number" name="patchpoints" id="patchpoints" class="form-control" step="1" min="0" max="48" />

                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>