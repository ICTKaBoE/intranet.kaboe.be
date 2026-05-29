<?php

const TEMPLATE = "
<div class='card mb-3'>
    <div class='card-header'>
        <h1 class='card-title'>@name@</h1>
    </div>

    <div class='card-body'>@info@</div>
    <div class='card-body'>Huidige waarde: @formatted.currentValue@</div>
</div>

<div class='card mb-3'>
    <div class='card-body'>
        <div role='chart' id='crt{{page:id}}' data-height='300vh' data-source='{{chart:url:full}}/{{url:part.id}}' data-no-data-text='Geen geschiedenis beschikbaar' data-title='Geschiedenis' data-xaxis-type='datetime'></div>
    </div>
</div>
";

?>

<div class="row" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}/{{url:part.id}}" data-template="<?= TEMPLATE; ?>"></div>