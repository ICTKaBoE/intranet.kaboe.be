<div class="card col-12 col-lg-6 mx-auto">
    <form class="card" action="{{form:url:full}}" method="POST" id="frm{{page:id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="school">Scholen</label>
                    <select name="school" id="school" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" multiple required></select>

                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="start" class="form-label">Start datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="start" id="start" class="form-control" required />
                    </div>

                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="end" class="form-label">Eind datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="end" id="end" class="form-control" required />
                    </div>

                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="per" class="form-label">Exporteer per</label>
                    <div>
                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="per" checked value="school" required>
                            <span class="form-check-label">School</span>
                        </label>

                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="per" value="teacher" required>
                            <span class="form-check-label">Leerkracht</span>
                        </label>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="exportAs">Exporteren als</label>
                    <div>
                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="exportAs" checked value="xlsx" required>
                            <span class="form-check-label">Excel</span>
                        </label>

                        <label class="form-check">
                            <input class="form-check-input" type="radio" name="exportAs" value="pdf" required>
                            <span class="form-check-label">PDF</span>
                        </label>
                    </div>

                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Exporteren</button>
        </div>
    </form>
</div>