<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto" <?php if (\Router\Helpers::getId() !== "add"): ?>data-prefill-id="{{url:part.id}}" data-locked-value="_lockedForm" <?php endif; ?>>
    <input type="hidden" name="schoolId" id="schoolId" />

    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="schoolyearId">Schooljaar</label>
                <select name="schoolyearId" id="schoolyearId" data-load-source="{{select:url:short}}/general/schoolyear" data-label="formatted.nameWithCurrent" data-default-value="{{schoolyear:default}}" required></select>
            </div>

            <div class="col-lg-4 col-12 mb-3">
                <label class="form-label" for="departmentId">Afdeling</label>
                <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-optgroup="schoolId" required data-on-change="departmentView"></select>
            </div>

            <div class="col-lg-4 col-12 mb-3">
                <label for="typeId" class="form-label">Type</label>
                <select name="typeId" id="typeId" data-load-source="{{select:url:short}}/{{url:part.module}}/type" data-parent-select="departmentId" data-on-change="typeView" data-default-no-load required></select>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label" for="courseId">Vak</label>
                <select name="courseId" id="courseId" data-load-source="{{select:url:short}}/school/course" data-search></select>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-12 mb-3">
                <label for="buildingId" class="form-label">Gebouw</label>
                <select name="buildingId" id="buildingId" data-load-source="{{select:url:short}}/management/building" data-default-no-load required></select>
            </div>

            <div class="col-lg-4 col-12 mb-3">
                <label for="roomId" class="form-label">Lokaal</label>
                <select name="roomId" id="roomId" data-load-source="{{select:url:short}}/management/room" data-label="formatted.name" data-parent-select="buildingId" data-default-no-load required></select>
            </div>

            <div class="col-12 col-lg-4 mb-3">
                <label for="seats" class="form-label">Totaal aantal plaatsen</label>
                <input type="number" name="seats" id="seats" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="description" class="form-label">Omschrijving</label>
                <textarea name="description" id="description" class="form-control" rows="5"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <label for="informatEmployeeId" class="form-label">Leerkracht</label>
                <select name="informatEmployeeId" id="informatEmployeeId" data-load-source="{{select:url:short}}/informat/employee" data-label="formatted.fullNameReversed" data-search></select>
            </div>
        </div>

        <?php if (\Router\Helpers::getId() === "add"): ?>
            <div class="row">
                <div class="col-12 col-lg-3 mb-3">
                    <label for="repeat" class="form-label">Herhaling</label>
                    <select name="repeat" id="repeat" data-on-change="repeatView">
                        <option value="N">Geen</option>
                        <option value="W">Wekelijks</option>
                    </select>
                </div>

                <div class="col-12 col-lg-5 mb-3">
                    <label for="repeatAt" class="form-label">Op</label>
                    <select name="repeatAt" id="repeatAt">
                        <option value="monday">Maandag</option>
                        <option value="tuesday">Dinsdag</option>
                        <option value="wednesday">Woensdag</option>
                        <option value="thursday">Donderdag</option>
                        <option value="friday">Vrijdag</option>
                    </select>
                </div>

                <div class="col-12 col-lg-4 mb-3">
                    <label for="hourId" class="form-label">Lesuur</label>
                    <select name="hourId" id="hourId" data-load-source="{{select:url:short}}/school/hours" data-label="formatted.startEnd" data-default-no-load required></select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label for="startDate" class="form-label">Startdatum (leeg = volgende of begin gekozen schooljaar)</label>
                    <input type="date" name="startDate" id="startDate" class="form-control" />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="endDate" class="form-label">Einddatum (leeg = einde gekozen schooljaar)</label>
                    <input type="date" name="endDate" id="endDate" class="form-control" disabled />
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label for="date" class="form-label">Datum</label>
                    <input type="date" name="date" id="date" class="form-control" disabled />
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="dayOfWeek" class="form-label">Op</label>
                    <select name="dayOfWeek" id="dayOfWeek">
                        <option value="1">Maandag</option>
                        <option value="2">Dinsdag</option>
                        <option value="3">Woensdag</option>
                        <option value="4">Donderdag</option>
                        <option value="5">Vrijdag</option>
                    </select>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label for="hourId" class="form-label">Lesuur</label>
                    <select name="hourId" id="hourId" data-load-source="{{select:url:short}}/school/hours" data-label="formatted.startEnd" data-default-no-load required></select>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-footer d-flex py-3">
        <button type="button" class="btn btn-link link-secondary ms-auto" onclick="history.back();">Annuleren</button>
        <button type="submit" class="btn btn-primary">Opslaan</button>
    </div>
</form>

<script>
    let add = "<?= (\Router\Helpers::getId() === "add"); ?>";
</script>