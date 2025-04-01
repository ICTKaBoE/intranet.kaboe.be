<?php

const TEMPLATE = "  <div class='col-12 col-md-6 col-lg-3 col-xl-2'>
                        <a href='@formatted.linkWithDefault@' target='@formatted.target@' class='card text-center bg-@color@ bg-gradient pb-0'>
                            <div class='card-body'> 
                                @formatted.icon.dashboard@
                            </div>
                            <div class='card-body border-top-0 pt-0'>
                                <h1>@name@</h1>
                            </div>
                        </a>
                    </div>";

?>

<div role="list" data-template="<?= TEMPLATE; ?>" data-source="{{list:url:full}}" class="row row-cards row-deck"></div>