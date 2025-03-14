<form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" class="row" data-prefill-id="{{url:part.id}}">
    <div class="col-12 col-lg-9">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Items</h2>
            </div>

            <table role="table" id="tbl{{page:id}}Line" data-source="{{table:url:full}}Line" data-no-paging data-no-info data-extra="[orderId={{url:part.id}}]"></table>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Details</h2>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" data-load-source="{{select:url:short}}/{{url:part.module}}/status" data-load-value="id" data-load-label="name" data-default-value="N" disabled></select>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="schoolId">School</label>
                    <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" disabled></select>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="creatorUserId">Aangemaakt door</label>
                    <select name="creatorUserId" id="creatorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" disabled></select>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="acceptorUserId">Goed te keuren door</label>
                    <select name="acceptorUserId" id="acceptorUserId" data-load-source="{{select:url:short}}/user" data-load-value="id" data-load-label="formatted.fullNameReversed" data-extra="[id={{module:acceptableUsers}}]" disabled></select>

                </div>

                <div class="mb-3">
                    <label class="form-label" for="supplierId">Leverancier</label>
                    <select name="supplierId" id="supplierId" data-load-source="{{select:url:short}}/{{url:part.module}}/supplier" data-load-value="id" data-load-label="name" disabled></select>

                </div>
            </div>
        </div>
    </div>
</form>