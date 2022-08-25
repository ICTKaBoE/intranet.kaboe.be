<form action="<?= Core\Page::formAction('POST'); ?>" method="post" class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-lg-2"><label class="form-label" for="priority">Prioriteit</label></div>
            <div class="col-lg-4">
                <select class="form-select" name="priority" id="priority" required>
                    <?php foreach (Core\Config::get("tool/helpdesk/priority") as $idx => $priority) : ?>
                        <option value="<?= $idx; ?>"><?= $priority['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback" data-feedback-input="priority"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-2"><label class="form-label" for="schoolId">School</label></div>
            <div class="col-lg-4">
                <select class="form-select" name="schoolId" id="schoolId" required>
                    <option value="0">--- Selecteer ---</option>
                    <?php foreach (Core\Config::get("schools") as $idx => $school) : ?>
                        <option value="<?= $idx; ?>"><?= $school; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback" data-feedback-input="schoolId"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-2"><label for="type" class="form-label">Type</label></div>
            <div class="col-lg-4">
                <select name="type" id="type" class="form-select" data-on-change="setCorrectFields" required>
                    <option value="0">--- Selecteer ---</option>
                    <?php foreach (Core\Config::get("tool/helpdesk/type") as $idx => $type) : ?>
                        <option value="<?= $idx; ?>"><?= $type; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback" data-feedback-input="type"></div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-2"><label for="deviceName" class="form-label">Toestelnaam</label></div>
            <div class="col-lg-4">
                <input type="text" name="deviceName" id="deviceName" class="form-control" required />
                <div class="invalid-feedback" data-feedback-input="deviceName"></div>
            </div>
            <div class="col-lg-6">
                <p data-for-type="LAPTOP" class="d-none form-remark">De naam van de laptop vindt u op de onderkant.</p>
                <p data-for-type="DESKTOP" class="d-none form-remark">De naam van de vaste computer vindt u op de sticker.</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-2"><label for="subject" class="form-label">Onderwerp</label></div>
            <div class="col-lg-6">
                <input type="text" name="subject" id="subject" class="form-control" required />
                <div class="invalid-feedback" data-feedback-input="subject"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2"><label for="message" class="form-label">Bericht</label></div>
            <div class="col-lg-6">
                <textarea name="message" id="message" class="form-control" rows="8" data-bs-toggle="autosize" required></textarea>
                <div class="invalid-feedback" data-feedback-input="message"></div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <div class="d-flex">
            <button type="submit" role="button" class="ms-auto btn btn-success">Indienen</button>
        </div>
    </div>
</form>