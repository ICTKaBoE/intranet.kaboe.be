<div class="card col-12 col-lg-6 mx-auto">
    <form action="{{form:url:full}}" method="post" autocomplete="off" id="frm{{page:id}}" data-prefill-id="{{url:part.id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="departmentId">Afdeling</label>
                    <select name="departmentId" id="departmentId" data-load-source="{{select:url:short}}/school/department" data-optgroup-attribute="schoolId" required></select>
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label class="form-label" for="name">Naam</label>
                    <input type="text" name="name" id="name" class="form-control" required />
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-12 mb-3">
                    <label for="from" class="form-label">Zichtbaar vanaf</label>
                    <input type="date" name="from" id="from" class="form-control" />
                </div>

                <div class="col-lg-6 col-12 mb-3">
                    <label for="until" class="form-label">Zichtbaar tot</label>
                    <input type="date" name="until" id="until" class="form-control" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="closeRegistrationAt" class="form-label">Sluit registratie x uren voor begin</label>
                    <input type="number" name="closeRegistrationAt" id="closeRegistrationAt" class="form-control" min="0" />
                </div>
            </div>

            <div class="row">
                <div class="col-12" id="chbCourseDependsOnSkore" role="checkbox" data-default-value="false" data-type="checkbox" data-name="courseDependsOnSkore" data-text="Getoonde vakken hangen af van Skore?"></div>
                <div class="col-12" id="chbAllowWithoutDate" role="checkbox" data-default-value="false" data-type="checkbox" data-name="allowWithoutDate" data-text="Toestaan dat er registraties kunnen worden gemaakt zonder datum?" data-on-change="checkAllowWithoutDate"></div>
                <div class="col-12" id="chbManualAssignDate" role="checkbox" data-default-value="false" data-type="checkbox" data-name="manualAssignDate" data-text="Leerling manueel toewijzen aan datum?" data-on-change="checkManualAssignDate"></div>
                <div class="col-12" id="chbAllowMultipleRegistrationsForThisTypeOnSameDay" role="checkbox" data-default-value="false" data-type="checkbox" data-name="allowMultipleRegistrationsForThisTypeOnSameDay" data-text="Toestaan dat er meerdere registraties van dit type voor dezelfde student op dezelfde datum toegestaan zijn?" data-on-change="checkAllowMultipleRegistrationsForThisTypeOnSameDay"></div>
                <div class="col-12" id="chbAllowMultipleRegistrationsForSameCourseOnSameDay" role="checkbox" data-default-value="false" data-type="checkbox" data-name="allowMultipleRegistrationsForSameCourseOnSameDay" data-text="Toestaan dat er meerdere registraties van hetzelfde vak op dezelfde datum toegestaan zijn?" data-on-change="checkAllowMultipleRegistrationsForSameCourseOnSameDay"></div>
                <div class="col-12" id="chbOnComputer" role="checkbox" data-default-value="false" data-type="checkbox" data-name="onComputer" data-text="Mogelijkheid tot het maken op computer?"></div>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="button" class="btn" onclick="history.back();">Annuleren</button>
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>