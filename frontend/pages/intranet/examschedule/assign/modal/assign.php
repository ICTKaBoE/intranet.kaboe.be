<div class="modal modal-blur fade" id="modal-assign" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uren toewijzen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="informatEmployeeId" id="informatEmployeeId">
                <input type="hidden" name="assignSchoolyearId" id="assignSchoolyearId" />

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="workedHours">Aantal uren gepresteerd</label>
                        <input type="number" name="workedHours" id="workedHours" class="form-control" required />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="examHours">Aantal uren examen</label>
                        <input type="number" name="examHours" id="examHours" class="form-control" required />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label" for="supervisionHours">Uren toezicht effectief</label>
                        <input type="number" name="supervisionHours" id="supervisionHours" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Ja</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nee</button>
            </div>
        </form>
    </div>
</div>