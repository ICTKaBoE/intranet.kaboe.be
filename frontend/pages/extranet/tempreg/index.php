<div class="row row-cards">
    <div class="col-md-5 m-auto">
        <form action="{{form:url:full}}add" method="post">
            <div class="card">
                <div class="card-body" id="tbl{{page:id}}">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="schoolId" class="form-label">Kies de school</label>
                            <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{url:param.schoolId}}" required></select>
                            <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="name" class="form-label">Uw naam</label>
                            <select name="name" id="name" data-load-source="{{select:url:short}}/{{url:part.module}}/person" data-load-value="name" data-load-label="name" data-extra="[schoolId={{url:param.schoolId}}]" data-parent-select="schoolId" required></select>
                            <div class="invalid-feedback" data-feedback-input="name"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">Temperaturen (°C)</div>

                        <div class="col-md-3">
                            <label for="soup" class="form-label">Soep</label>
                            <input type="number" step="0.1" name="soup" id="soup" class="form-control" />
                            <div class="invalid-feedback" data-feedback-input="soup"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="pasta" class="form-label">Aardappel, pasta, rijst, ...</label>
                            <input type="number" step="0.1" name="pasta" id="pasta" class="form-control" />
                            <div class="invalid-feedback" data-feedback-input="pasta"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="vegetables" class="form-label">Groente</label>
                            <input type="number" step="0.1" name="vegetables" id="vegetables" class="form-control" />
                            <div class="invalid-feedback" data-feedback-input="vegetables"></div>
                        </div>

                        <div class="col-md-3">
                            <label for="meat" class="form-label">Vlees/Vis</label>
                            <input type="number" step="0.1" name="meat" id="meat" class="form-control" />
                            <div class="invalid-feedback" data-feedback-input="meat"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label for="notes" class="form-label">Opmerking</label>
                            <input type="text" name="notes" id="notes" class="form-control" />
                            <div class="invalid-feedback" data-feedback-input="notes"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Opslaan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>