<div class="card col-12 col-lg-6 mx-auto">
    <form class="card" action="{{form:url:full}}" method="POST" id="frm{{page:id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="school">Scholen</label>
                    <select name="school" id="school" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" multiple required></select>
                    <div class="invalid-feedback" data-feedback-input="school"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="start" class="form-label">Start datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="start" id="start" class="form-control" required />
                    </div>
                    <div class="invalid-feedback" data-feedback-input="start"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="end" class="form-label">Eind datum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                        <input role="datepicker" name="end" id="end" class="form-control" required />
                    </div>
                    <div class="invalid-feedback" data-feedback-input="end"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="type" class="form-label">Type</label>
                    <div id="chbType" role="checkbox" data-type="radio" data-name="type" data-value="HW|WW" data-text="Woon-Werk|Werk-Werk" data-default-value="HW"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="per" class="form-label">Exporteer per</label>
                    <div id="chbPer" role="checkbox" data-type="radio" data-name="per" data-value="school|teacher" data-text="School|Leerkracht" data-default-value="school"></div>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label class="form-label" for="exportAs">Exporteren als</label>
                    <div id="chbExportAs" role="checkbox" data-type="radio" data-name="exportAs" data-value="xlsx|pdf" data-text="Excel|PDF" data-default-value="xlsx"></div>
                    <div class="invalid-feedback" data-feedback-input="exportAs"></div>
                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">Exporteren</button>
        </div>
    </form>
</div>