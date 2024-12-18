<div class="row">
    <div class="col-12 col-md-9">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="card-title">Info</h2>
            </div>

            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Naam</div>
                        <div class="datagrid-content">{{staff:name}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Voornaam</div>
                        <div class="datagrid-content">{{staff:firstName}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Stamnummer</div>
                        <div class="datagrid-content">{{staff:basenumber}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Schooljaar</div>
                        <div class="datagrid-content">{{staff:formatted.badge.schoolyear}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title"><?= \Helpers\HTML::Icon("school", class: ["me-2"]); ?>School</div>
                        <div class="datagrid-content">{{staff:formatted.badge.schools}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title"><?= \Helpers\HTML::Icon("briefcase-2", class: ["me-2"]); ?>Functies</div>
                        <div class="datagrid-content">{{staff:formatted.functions}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title"><?= \Helpers\HTML::Icon("phone", class: ["me-2"]); ?>Telefoon</div>
                        <div class="datagrid-content">{{staff:formatted.phone}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title"><?= \Helpers\HTML::Icon("mail", class: ["me-2"]); ?>E-mail</div>
                        <div class="datagrid-content">{{staff:formatted.email}}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Adres</div>
                        <div class="datagrid-content">{{staff:formatted.address}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none d-md-block col-3">
        <div class="card">
            <img class="img-fluid rounded" src="{{site:url}}/frontend/shared/default/images/informat/employee/{{staff:informatGuid}}.jpg" alt="{{staff:formatted.fullNameReversed}}">
        </div>
    </div>
</div>