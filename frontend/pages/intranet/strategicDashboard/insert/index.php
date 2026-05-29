<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School (geen = algemeen)</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/all" data-default-value="{{user:mainSchoolId}}"></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="itemId">Item</label>
                    <select name="itemId" id="itemId" data-load-source="{{select:url:short}}/{{url:part.module}}/items" data-parent-select="schoolId" data-extra="[schoolId=0]" data-on-change="itemView" required></select>
                </div>
            </div>

            <div class="row" id="valueContainer"></div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>