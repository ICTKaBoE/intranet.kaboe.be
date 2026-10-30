<?php

// const TEMPLATE = "  <div class='col-12 col-md-6 col-lg-3 col-xl-2'>
//                         <a href='@formatted.linkWithDefault@' target='@formatted.target@' class='card text-center bg-@color@ bg-gradient pb-0'>
//                             @formatted.badge@
//                             <div class='card-body card-img-top'> 
//                                 @formatted.icon.dashboard@
//                             </div>
//                             <div class='card-body border-top-0 pt-0'>
//                                 <h1>@name@</h1>
//                             </div>
//                         </a>
//                     </div>";

const TEMPLATE = "
<div class='col-12 col-md-6 col-lg-3 col-xl-2'>
    <a href='@formatted.linkWithDefault@' target='@formatted.target@' class='card card-link card-link-rotate bg-@color@ bg-gradient'>
        @formatted.badge@
        <div class='card-body'>
            <div class='row h-100 align-items-center'>
                <div class='col-auto'>@formatted.icon.dashboard@</div>
                <div class='col'><h1>@name@</h1></div>
            </div>
        </div>
    </a>
</div>
";

?>

<div role="list" id="lstHome" data-template="<?= TEMPLATE; ?>" data-source="{{list:url:full}}" class="row row-cards row-deck"></div>