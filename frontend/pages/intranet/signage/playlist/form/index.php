<?php if (\Router\Helpers::getId() === "add"): ?>
    <div class="card col-12 col-lg-6 mx-auto">
        <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label class="form-label" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school/" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" data-on-change="assignedToView" required></select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="name" class="form-label">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" required />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-12 mb-3">
                        <label for="assignedTo" class="form-label">Toegewezen aan</label>
                        <select name="assignedTo" id="assignedTo" data-on-change="assignedToView" required>
                            <option value="S">Scherm</option>
                            <option value="G">Groep</option>
                        </select>
                    </div>

                    <div class="col-lg-6 col-12 mb-3">
                        <label for="assignedToId" class="form-label">Toegewezen aan</label>
                        <select name="assignedToId" id="assignedToId" data-load-source="[S@{{select:url:short}}/{{url:part.module}}/screen;G@{{select:url:short}}/{{url:part.module}}/group]" data-load-value="id" data-load-label="name" required></select>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="button" class="btn" onclick="history.back();">Annuleren</button>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row" data-prefill-id="{{url:part.id}}" data-locked-value="_lockedForm">
        <div class="col-12 col-lg-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Media</h2>
                </div>

                <table role="table" id="tbl{{page:id}}Item" data-source="{{table:url:full}}Item" data-no-paging data-no-info data-double-click-action="edit" data-extra="[playlistId={{url:part.id}}]"></table>
            </div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Details</h2>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-on-change="assignedToView" required></select>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Naam</label>
                        <input type="text" name="name" id="name" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="assignedTo" class="form-label">Toegewezen aan</label>
                        <select name="assignedTo" id="assignedTo" data-on-change="assignedToView" required>
                            <option value="S">Scherm</option>
                            <option value="G">Groep</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignedToId" class="form-label">Toegewezen aan</label>
                        <select name="assignedToId" id="assignedToId" data-load-source="[S@{{select:url:short}}/{{url:part.module}}/screen;G@{{select:url:short}}/{{url:part.module}}/group]" data-load-value="id" data-load-label="name" required></select>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<script>
    let add = "<?= (\Router\Helpers::getId() === "add"); ?>";
    let orderId = "<?= \Router\Helpers::getId(); ?>";
</script>