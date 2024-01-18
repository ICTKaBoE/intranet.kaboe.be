<div class="row row-cards">
    <div class="col-12">
        <form action="{{form:url:full}}" method="post">
            <div class="card">
                <div class="card-body" id="tbl{{page:id}}">
                    <div class="mb-3">
                        <label for="schoolId" class="form-label">Kies jouw school</label>
                        <select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required data-default-value="{{user:profile:mainSchoolId}}"></select>
                        <div class="invalid-feedback" data-feedback-input="schoolId"></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="studentOrStaff" class="form-label">Kies student of personeel</label>
                            <select name="studentOrStaff" id="studentOrStaff" data-on-change="check" required>
                                <option value=""></option>
                                <option value="student">Student</option>
                                <option value="staff">Personeel</option>
                            </select>
                            <div class="invalid-feedback" data-feedback-input="studentOrStaff"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="personId" class="form-label">Kies de terugbrenger</label>
                            <select name="personId" id="personId" data-load-source="[student@{{select:url:short}}/informat/students;staff@{{select:url:short}}/informat/staff]" data-load-value="id" data-load-label="fullName" data-parent-select="schoolId" data-search required></select>
                            <div class="invalid-feedback" data-feedback-input="personId"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bookId" class="form-label">Kies jouw boek(en)</label>
                        <select name="bookId" id="bookId" data-load-source="{{select:url:short}}/{{url:part:module}}/lend" data-load-value="id" data-load-label="lendTitle" data-parent-select="schoolId" multiple data-search required></select>
                        <div class="invalid-feedback" data-feedback-input="bookId"></div>
                    </div>
                    <div class="modal-footer" data-form-type="post-return">
                        <button type="submit" class="btn btn-danger">Breng boek(en) terug</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>