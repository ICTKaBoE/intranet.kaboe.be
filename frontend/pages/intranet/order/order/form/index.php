<?php
const ATTACHMENT_TEMPLATE = "   <div class='row mb-1'>
                                    <div>@link@</div>
                                </div>";
?>

<?php if (\Router\Helpers::getId() === "add"): ?>
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="card col-12 col-lg-6 mx-auto">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name" data-default-value="N" required></select>

                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" data-default-value="{{user:mainSchoolId}}" required></select>

                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="acceptorUserId">Goed te keuren door</label>
                    <select name="acceptorUserId" id="acceptorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[id={{module:acceptableUsers}}]" data-search multiple required></select>

                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <label class="form-label" for="supplierId">Leverancier</label>
                    <select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/{{url:part.module}}/supplier" data-load-value="id" data-load-label="name" data-search required></select>

                </div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
<?php else: ?>
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row" data-prefill-id="{{url:part.id}}" data-locked-value="_lockedForm">
        <div class="col-12 col-lg-9">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Items</h2>
                </div>

                <table role="table" id="tbl{{page:id}}Line" data-source="{{table:url:full}}Line" data-no-paging data-no-info data-double-click-action="edit" data-extra="[orderId={{url:part.id}}]"></table>
            </div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Offerte</h2>
                </div>

                <div class="card-body">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="quoteLink">Link</label>
                        <input type="text" class="form-control" id="quoteLink" name="quoteLink">
                    </div>

                    <div class="col-12 mb-3">
                        <label for="quoteFile" class="form-label">Bestand</label>
                        <input type="file" role="file" name="quoteFile" id="quoteFile" class="form-control">
                    </div>
                </div>

                <div class="card-body" role="list" id="lstQuotes{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/quotes" data-template="<?= ATTACHMENT_TEMPLATE; ?>" data-extra="[orderId={{url:part.id}}]"></div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Details</h2>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name" data-default-value="N" data-no-lock></select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="schoolId">School</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="creatorUserId">Aangemaakt door</label>
                        <select name="creatorUserId" id="creatorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" disabled></select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="acceptorUserId">Goed te keuren door</label>
                        <select name="acceptorUserId" id="acceptorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[id={{module:acceptableUsers}}]" required></select>

                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="supplierId">Leverancier</label>
                        <select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/{{url:part.module}}/supplier" data-load-value="id" data-load-label="name" required></select>

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