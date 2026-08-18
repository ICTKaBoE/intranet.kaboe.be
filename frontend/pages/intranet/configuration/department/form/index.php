<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label for="schoolId" class="form-label">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-on-change="schoolIdView"></select>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="managementRoomId" class="form-label">Lokalen</label>
                    <select name="managementRoomId" id="managementRoomId" data-load-source="{{select:url:short}}/management/room" data-label="formatted.buildingRoom" data-search multiple></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="informatClassId" class="form-label">Klassen</label>
                    <select name="informatClassId" id="informatClassId" data-load-source="{{select:url:short}}/informat/classgroup" data-search multiple></select>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>