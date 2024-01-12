<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <label for="sel{{page:id}}" class="form-label">Kies jouw school</label>
                <select name="sel{{page:id}}" id="sel{{page:id}}" data-load-source="{{select:url:short}}/school" data-default-value="{{user:profile:mainSchoolId}}" data-load-value="id" data-load-label="name" data-on-change="loadTable" required></select>
            </div>

            <table role="table" id="tbl{{page:id}}" data-source="{{table:url:short}}/notescreen/articles" data-double-click="edit"></table>
        </div>
    </div>
</div>