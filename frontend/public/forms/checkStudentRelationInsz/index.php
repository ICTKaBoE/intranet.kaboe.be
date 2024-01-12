<div class="row row-cards">
	<div class="col-md-5 m-auto">
		<form action="{{form:url:full}}" method="post">
			<div class="card">
				<div class="card-body" id="tbl{{page:id}}">
					<div class="row mb-3">
						<div class="col-md-4">
							<label for="schoolId" class="form-label">Kies de school van uw kind</label>
							<select name="schoolId" id="schoolId" data-load-source="{{select:url:short}}/school" data-load-value="id" data-load-label="name" required></select>
							<div class="invalid-feedback" data-feedback-input="schoolId"></div>
						</div>

						<div class="col-md-4">
							<label for="classId" class="form-label">Kies de klas van uw kind</label>
							<select name="classId" id="classId" data-load-source="{{select:url:short}}/class" data-load-value="id" data-load-label="nameWithTeacher" data-parent-select="schoolId" data-on-change="loadStudents" data-search required></select>
							<div class="invalid-feedback" data-feedback-input="classId"></div>
						</div>

						<div class="col-md-4">
							<label for="studentId" class="form-label">Kies uw kind</label>
							<select name="studentId" id="studentId" data-load-source="{{select:url:short}}/synchronisation/student" data-load-value="id" data-load-label="displayNameReversed" data-search required></select>
							<div class="invalid-feedback" data-feedback-input="studentId"></div>
						</div>
					</div>

					<div class="row mb-3">

					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Verzenden</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>