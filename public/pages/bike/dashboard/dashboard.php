<?php
$profile = (new \Database\Repository\BikeProfile)->getByUpn(\Security\Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
if (is_null($profile)) \Security\Session::set(SECURITY_SESSION_PAGEERROR, "Gelieve eerst je profiel in te vullen!");
?>

<div class="row row-cards">

</div>