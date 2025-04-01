<?php
const STAFF_TEMPLATE = "<div class='col-12 col-md-6 col-lg-3'>
                            <a href='{{site:url}}/{{url:part.module}}/{{url:part.page}}/@formatted.informatGuidOrId@' class='card'>
                                <div class='card-body p-4 text-center'>
                                    <span class='avatar avatar-xl mb-3 rounded' style='background-image: url({{site:url}}/frontend/shared/default/images/informat/employee/@informatGuid@.jpg)'>@formatted.initialsIfNoPhoto@</span>
                                    <h3 class='m-0 mb-1'>@formatted.fullNameReversed@</h3>
                                    <div class='text-muted m-0 mb-1'>@formatted.functionsPerSchool@</div>
                                </div>
                            </a>
                        </div>";
?>

<div role="list" id="lst{{page:id}}" class="row row-cards" data-source="{{list:url:full}}" data-template="<?= STAFF_TEMPLATE; ?>"></div>