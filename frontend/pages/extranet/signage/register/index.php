<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Registreer uw scherm</h2>
            </div>

            <div class="card-body">
                <p>Gebruik volgende code om uw scherm te registreren</p>
                <b><?= \Router\Helpers::url()->getParam("code"); ?></b>
            </div>
        </div>
    </div>
</div>