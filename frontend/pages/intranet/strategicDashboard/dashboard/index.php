<?php
const TEMPLATE_CARD = "
    <div class='col-12 col-lg-@width@'>
        <div class='card mb-3'>
            <div class='card-header'>
                @linked.school.formatted.badge.name@
                <h1 class='card-title ms-2'>@name@</h1>
            </div>
            
            <div class='card-body'>@formatted.html@</div>
            
            <div class='card-footer'><span>Mimimum waarde: @minimum@</span><br /><span>Streefwaarde: @target@</span></div>
        </div>
    </div>";
?>

<div class="row" role="list" id="lst{{page:id}}" data-source="{{list:url:full}}" data-template="<?= TEMPLATE_CARD; ?>"></div>