<?php
const LIST_TEMPLATE = " <div class='list-group-item drop-event cursor-move py-1 px-3' data-id='@id@' data-title='@linked.informatStudent.formatted.fullNameReversed@ (@linked.classgroup.name@)' data-color='lime'>
                            <div class='row align-items-center'>
                                <div class='col-auto'><span class='avatar rounded' style='background-image: url({{site:url}}/frontend/shared/default/images/informat/student/@linked.informatStudent.informatGuid@.jpg)'>@linked.informatStudent.formatted.initialsIfNoPhoto@</span></div>
                                <div class='col-6'>
                                    <h3 class='m-0'>@linked.informatStudent.formatted.fullNameReversed@ (@linked.classgroup.name@)</h3>
                                    <div class='text-muted m-0'>@linked.course.name@</div>
                                </div>
                                <div class='col-auto'>@remark@</div>
                            </div>
                        </div>";
?>

<div class="row">
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-body">
                <div id="cal{{page:id}}" role="calendar" data-source="[{{calendar:url:full}},{{calendar:url:short}}/holliday]" data-event-remove="removeEvent" data-on-drop="dropEvent" data-event-data-format="eventDataFormat" data-action="{{calendar:url:full}}" data-editable data-droppable data-event-container="lst{{page:id}}"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Mededeling</h4>
            </div>

            <div class="card-body">
                <p>
                    Bij het toevoegen, verplaatsen of verwijderen van een leerling aan een bepaalde inhaalles, worden zowel ouders als de leerling op de hoogte gebracht via een Smartschool-bericht.<br />
                    Probeer verplaatsingen dus tot een minimum te beperken.
                </p>
            </div>
        </div>

        <div class="card mb-3 removeEventContainer">
            <div class="card-header">
                <h4 class="card-title">Leerlingen</h4>
            </div>

            <div class="list-group list-group-flush list-group-hoverable overflow-auto" role="list" id="lst{{page:id}}" data-source="{{list:url:short}}/{{url:part.module}}/notAssigned" data-template="<?= LIST_TEMPLATE; ?>"></div>
        </div>
    </div>
</div>

<script>
    let calendarId = "cal{{page:id}}";
</script>