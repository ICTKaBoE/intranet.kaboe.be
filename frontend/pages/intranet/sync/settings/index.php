<form action="{{form:url:full}}" class="row" method="post" id="frm{{page:id}}" data-prefill>
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Informat - Eigen velden - Tewerkstelling</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="informat.ownfield.status">Status</label>
                    <input type="text" name="informat.ownfield.status" id="informat.ownfield.status" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="informat.ownfield.mainSchool">Hoofdschool</label>
                    <input type="text" name="informat.ownfield.mainSchool" id="informat.ownfield.mainSchool" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="informat.ownfield.createEmailWith">E-mail aanmaken met...</label>
                    <input type="text" name="informat.ownfield.createEmailWith" id="informat.ownfield.createEmailWith" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Informat - Foto's</h2>
            </div>

            <div class="card-body">
                <div class="col-12" id="chbEmployee" role="checkbox" data-type="checkbox" data-name="photo.employee" data-text="Personeel"></div>
                <div class="col-12" id="chbStudent" role="checkbox" data-type="checkbox" data-name="photo.student" data-text="Leerling"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Format</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="format.displayName">Naam weergave</label>
                    <input type="text" name="format.displayName" id="format.displayName" class="form-control" required />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="format.email">E-Mail</label>
                    <input type="text" name="format.email" id="format.email" class="form-control" required />
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Wachtwoord</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="default.password.expiration.days">Aantal dagen geldig</label>
                    <select name="default.password.expiration.days" id="default.password.expiration.days">
                        <option value="">Selecteer hoeveelheid...</option>

                        <optgroup label="Dagen">
                            <option value="1 day">1 dag</option>
                            <option value="2 days">2 dagen</option>
                            <option value="3 days">3 dagen</option>
                            <option value="4 days">4 dagen</option>
                            <option value="5 days">5 dagen</option>
                            <option value="6 days">6 dagen</option>
                        </optgroup>

                        <optgroup label="Weken">
                            <option value="1 week">1 week</option>
                            <option value="2 weeks">2 weken</option>
                            <option value="3 weeks">3 weken</option>
                            <option value="4 weeks">4 weken</option>
                        </optgroup>

                        <optgroup label="Maanden">
                            <option value="1 month">1 maand</option>
                            <option value="2 months">2 maanden</option>
                            <option value="3 months">3 maanden</option>
                            <option value="4 months">4 maanden</option>
                            <option value="5 months">5 maanden</option>
                            <option value="6 months">6 maanden</option>
                            <option value="7 months">7 maanden</option>
                            <option value="8 months">8 maanden</option>
                            <option value="9 months">9 maanden</option>
                            <option value="10 months">10 maanden</option>
                            <option value="11 months">11 maanden</option>
                        </optgroup>

                        <optgroup label="Jaren">
                            <option value="1 year">1 jaar</option>
                            <option value="2 year">2 jaren</option>
                            <option value="3 year">3 jaren</option>
                        </optgroup>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="default.password.expiration.start">Melden aantal dagen vooraf</label>
                    <select name="default.password.expiration.start" id="default.password.expiration.start">
                        <option value="">Selecteer hoeveelheid...</option>

                        <optgroup label="Dagen">
                            <option value="1 day">1 dag</option>
                            <option value="2 days">2 dagen</option>
                            <option value="3 days">3 dagen</option>
                            <option value="4 days">4 dagen</option>
                            <option value="5 days">5 dagen</option>
                            <option value="6 days">6 dagen</option>
                        </optgroup>

                        <optgroup label="Weken">
                            <option value="1 week">1 week</option>
                            <option value="2 weeks">2 weken</option>
                            <option value="3 weeks">3 weken</option>
                            <option value="4 weeks">4 weken</option>
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Personeel IN DIENST - Bericht naar Privé E-Mail</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="informat.mailType">Type E-mail in Informat</label>
                    <input type="text" name="informat.mailType" id="informat.mailType" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.employee.subject">Onderwerp</label>
                    <input type="text" name="mail.template.employee.subject" id="mail.template.employee.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.employee.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.employee.body" id="mail_template_employee_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Personeel IN DIENST - Bericht naar Centrale Dienst</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.employeecentral.subject">Onderwerp</label>
                    <input type="text" name="mail.template.employeecentral.subject" id="mail.template.employeecentral.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.employeecentral.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.employeecentral.body" id="mail_template_employeecentral_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Personeel UIT DIENST - Bericht naar Privé E-Mail</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.employeedisable.subject">Onderwerp</label>
                    <input type="text" name="mail.template.employeedisable.subject" id="mail.template.employeedisable.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.employeedisable.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.employeedisable.body" id="mail_template_employeedisable_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Personeel UIT DIENST - Bericht naar Centrale Dienst</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.employeedisablecentral.subject">Onderwerp</label>
                    <input type="text" name="mail.template.employeedisablecentral.subject" id="mail.template.employeedisablecentral.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.employeedisablecentral.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.employeedisablecentral.body" id="mail_template_employeedisablecentral_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Personeel Wachtwoord Vervallen</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.password.subject">Onderwerp</label>
                    <input type="text" name="mail.template.password.subject" id="mail.template.password.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.password.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.password.body" id="mail_template_password_body" class="form-control">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Berichten - Leerling naar Secretariaat</h2>
            </div>

            <div class="card-body">
                <div class="col-12 mb-3">
                    <label class="form-label" for="mail.template.student.subject">Onderwerp</label>
                    <input type="text" name="mail.template.student.subject" id="mail.template.student.subject" class="form-control" />
                </div>

                <div class="col-12 mb-3">
                    <label for="mail.template.student.body" class="form-label">Body</label>
                    <input type="text" role="tinymce" name="mail.template.student.body" id="mail_template_student_body" class="form-control">
                </div>
            </div>
        </div>
    </div>
</form>