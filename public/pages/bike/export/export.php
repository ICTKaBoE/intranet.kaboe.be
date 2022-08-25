<div class="row row-cards">
    <div class="col-md-3">
        <form method="EXPORT" action="<?= Core\Page::formAction('EXPORT', 'perTeacher'); ?>" id="<?= Core\Page::id('tbl', 'perTeacher'); ?>" class="card">
            <div class="card-header">
                <h4 class="card-title">Per leerkracht</h4>
            </div>

            <div class="card-body">
                <p>Een overzicht van de gereden kilometers, per leerkracht.</p>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="perTeacherStart" class="form-label">Startdatum</label>
                    <div class="input-icon">
                        <input role="datepicker" name="perTeacherStart" id="perTeacherStart" class="form-control" required />
                        <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="perTeacherEnd" class="form-label">Einddatum</label>
                    <div class="input-icon">
                        <input role="datepicker" name="perTeacherEnd" id="perTeacherEnd" class="form-control" required />
                        <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex">
                    <!-- <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="teacherAsPdf" id="teacherAsPdf" checked>
                        <span class=" form-check-label">Exporteer als PDF</span>
                    </label> -->

                    <button class="btn btn-success ms-auto" role="button">Exporteren</button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-3">
        <form method="EXPORT" action="<?= Core\Page::formAction('EXPORT', 'perSchool'); ?>" id="<?= Core\Page::id('tbl', 'perSchool'); ?>" class="card">
            <div class="card-header">
                <h4 class="card-title">Per school</h4>
            </div>

            <div class="card-body">
                <p>Een overzicht van de gereden kilometers, per school.</p>
            </div>

            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="perSchoolStart" class="form-label">Startdatum</label>
                    <div class="input-icon">
                        <input role="datepicker" name="perSchoolStart" id="perSchoolStart" class="form-control" required />
                        <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="perSchoolEnd" class="form-label">Einddatum</label>
                    <div class="input-icon">
                        <input role="datepicker" name="perSchoolEnd" id="perSchoolEnd" class="form-control" required />
                        <span class="input-icon-addon"><?= Helpers\Icon::load("calendar"); ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="schoolAsPdf" id="schoolAsPdf" checked>
                        <span class=" form-check-label">Exporteer als PDF</span>
                    </label>

                    <button class="btn btn-success ms-auto" role="button">Exporteren</button>
                </div>
            </div>
        </form>
    </div>
</div>