<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="alias" class="form-label">Alias</label>
                    <input type="text" name="alias" id="alias" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="type">Type</label>
                    <select name="type" id="type" data-on-change="typeView" required>
                        <option value="I">Image</option>
                        <option value="V">Video</option>
                        <option value="L">Link</option>
                    </select>
                </div>
            </div>

            <div class="row" id="type-I">
                <div class="col-12 mb-3">
                    <label for="mediaImage" class="form-label">Image</label>
                    <input type="file" name="mediaImage" id="mediaImage" class="form-control" required />
                </div>
            </div>

            <div class="row d-none" id="type-V">
                <div class="col-12 mb-3">
                    <label for="mediaVideo" class="form-label">Video</label>
                    <input type="file" name="mediaVideo" id="mediaVideo" class="form-control" required />
                </div>
            </div>

            <div class="row d-none" id="type-L">
                <div class="col-12 mb-3">
                    <label for="mediaLink" class="form-label">Link</label>
                    <input type="url" name="mediaLink" id="mediaLink" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>