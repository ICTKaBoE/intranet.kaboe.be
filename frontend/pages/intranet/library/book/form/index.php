<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>

                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="categoryId">Categorie</label>
                    <select name="categoryId" id="categoryId" data-load-source="{{select:url:short}}/{{url:part.module}}/category" data-load-value="id" data-load-label="name" required></select>

                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="authorId">Auteur</label>
                    <select name="authorId" id="authorId" data-load-source="{{select:url:short}}/{{url:part.module}}/author" data-load-value="id" data-load-label="name" data-search required></select>

                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label" for="title">Titel</label>
                    <input type="text" name="title" id="title" class="form-control" required />

                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>