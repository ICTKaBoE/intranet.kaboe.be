<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="from">Van</label>
                    <input type="datetime-local" name="from" id="from" class="form-control" required />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="until">Tot</label>
                    <input type="datetime-local" name="until" id="until" class="form-control" />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="type">Type</label>
                    <select name="type" id="type" data-load-source="{{select:url:full}}Type" data-load-value="id" data-load-label="name" required></select>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="navigationId">Tonen op</label>
                    <select name="navigationId" id="navigationId" data-load-source="{{select:url:short}}/navigation" data-load-value="id" data-load-label="name" required></select>
                </div>

                <div class="col-12">
                    <label class="form-label" for="content">Inhoud</label>
                    <textarea type="text" name="content" id="content" class="form-control" rows="5" required></textarea>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>