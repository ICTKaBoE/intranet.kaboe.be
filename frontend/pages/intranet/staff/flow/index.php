<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill class="row row-cards">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Nieuw item</h1>
        </div>

        <div class="card-body">
            <h1 class="card-title">Algemeen</h1>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="flow.new.subject" class="form-label">Onderwerp</label>
                    <input type="text" name="flow.new.subject" id="flow.new.subject" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label for="flow.new.body" class="form-label">Inhoud</label>
                    <input type="text" role="tinymce" name="flow.new.body" id="flow_new_body" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <h1 class="card-title">Altijd uitvoeren</h1>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="flow.new.to" class="form-label">Ontvanger (gescheiden door komma's)</label>
                    <input type="text" name="flow.new.to" id="flow.new.to" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <input type="hidden" name="flow.new.filtered" id="flow.new.filtered">
            <h1 class="card-title">Gefilterd uitvoeren</h1>

            <div class="row mb-3">
                <div class="col-lg-5 col-12">
                    <h1 class="form-label">Functie(s)</h1>
                </div>
                <div class="col-lg-7 col-12">
                    <h1 class="form-label">Ontvanger (gescheiden door komma's)</h1>
                </div>
            </div>

            <div id="new_filtered_container">
                <div class="row mb-1 d-none" id="new_filtered_<INDEX>">
                    <div class="col-lg-5 col-12">
                        <select data-no-create name="flow_new_roleId_<INDEX>" id="flow_new_roleId_<INDEX>" data-load-source="{{select:url:short}}/{{url:part.module}}/role" data-render-item="renderOptgroupItem" data-default-value="<ROLE_ID_DEFAULTVALUE>" multiple></select>
                    </div>

                    <div class="col-lg-6 col-12">
                        <input type="text" name="flow_new_to_<INDEX>" id="flow_new_to_<INDEX>" class="form-control" value="<TO_DEFAULTVALUE>" required />
                    </div>

                    <div class="col">
                        <button type="button" id="btnDeleteNew<INDEX>" class="btn btn-icon btn-danger"><i class="icon ti ti-trash"></i></button>
                    </div>
                </div>
            </div>

            <button id="btnAddNew"></button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Item bewerken</h1>
        </div>

        <div class="card-body">
            <h1 class="card-title">Algemeen</h1>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="flow.edit.subject" class="form-label">Onderwerp</label>
                    <input type="text" name="flow.edit.subject" id="flow.edit.subject" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label for="flow.edit.body" class="form-label">Inhoud</label>
                    <input type="text" role="tinymce" name="flow.edit.body" id="flow_edit_body" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <h1 class="card-title">Altijd uitvoeren</h1>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="flow.edit.to" class="form-label">Ontvanger (gescheiden door komma's)</label>
                    <input type="text" name="flow.edit.to" id="flow.edit.to" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card-body">
            <input type="hidden" name="flow.edit.filtered" id="flow.edit.filtered">
            <h1 class="card-title">Gefilterd uitvoeren</h1>

            <div class="row mb-3">
                <div class="col-lg-5 col-12">
                    <h1 class="form-label">Functie(s)</h1>
                </div>
                <div class="col-lg-7 col-12">
                    <h1 class="form-label">Ontvanger (gescheiden door komma's)</h1>
                </div>
            </div>

            <div id="edit_filtered_container">
                <div class="row mb-1 d-none" id="edit_filtered_<INDEX>">
                    <div class="col-lg-5 col-12">
                        <select data-no-create name="flow_edit_parameter_<INDEX>" id="flow_edit_parameter_<INDEX>" data-load-source="{{select:url:short}}/{{url:part.module}}/parameter" data-render-item="renderOptgroupItem" data-default-value="<PARAMETER_DEFAULTVALUE>" multiple></select>
                    </div>

                    <div class="col-lg-6 col-12">
                        <input type="text" name="flow_edit_to_<INDEX>" id="flow_edit_to_<INDEX>" class="form-control" value="<TO_DEFAULTVALUE>" required />
                    </div>

                    <div class="col">
                        <button type="button" id="btnDeleteEdit<INDEX>" class="btn btn-icon btn-danger"><i class="icon ti ti-trash"></i></button>
                    </div>
                </div>
            </div>

            <button id="btnAddEdit"></button>
        </div>
    </div>
</form>