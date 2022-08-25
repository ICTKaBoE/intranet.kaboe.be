<?php

use Helpers\Date;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

$profile = (new \Database\Repository\BikeProfile)->getByUpn(\Security\Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn']);
if (is_null($profile)) \Security\Session::set(SECURITY_SESSION_PAGEERROR, "Gelieve eerst je profiel in te vullen!");

$blockPastRegistrations = (Strings::equal(Core\Config::get("tool/bike/blockPastRegistration"), true) || Strings::equal(Core\Config::get("tool/bike/blockPastRegistration"), 'on'));
$blockFutureRegistrations = (Strings::equal(Core\Config::get("tool/bike/blockFutureRegistration"), true) || Strings::equal(Core\Config::get("tool/bike/blockFutureRegistration"), 'on'));
$blockFuture = $blockFutureRegistrations ? Clock::now()->plusDays(1)->format("Y-m-d") : null;
?>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="card-title">Mededeling</h4>
            </div>

            <div class="card-body">
                <p>
                    Bij wijziging van je profiel, moeten alle fietsdagen van vóór de wijziging ingevoerd zijn.<br />
                    Pas daarna pas je je profiel aan.
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Legenda</h4>
            </div>

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-2 bg-<?= Core\Config::get('tool/bike/colorDistance1'); ?>"></div>
                    <div class="col">Afstand 1</div>
                </div>

                <div class="row mb-2">
                    <div class="col-2 bg-<?= Core\Config::get('tool/bike/colorDistance2'); ?>"></div>
                    <div class="col">Afstand 2</div>
                </div>

                <div class="row">
                    <div class="col-2 bg-blue">~~~~</div>
                    <div class="col">Feestdag / Schoolvakantie</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <div role="calendar" id="<?= Core\Page::id('cal'); ?>" data-action="<?= Core\Page::formAction('POST'); ?>" data-source="[<?= Core\Page::formAction('GET', 'holliday'); ?>,<?= Core\Page::formAction('GET'); ?>]" data-date-click="setDistance" <?php if ($blockFutureRegistrations) : ?>data-range-end="<?= $blockFuture; ?>" <?php endif; ?>></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Handleiding</h3>
            </div>
            <div class="card-body">
                <p>Klik op de dag(en) waarop je met de fiets kwam. 1e klik = aan (afstand 1), 2e klik = aan (afstand 2), 3e klik = uit.</p>
                <p>Om te veranderen van maand of jaar: klik op de pijltjes in de hoeken.</p>
            </div>
            <?php if ($blockFutureRegistrations) : ?>
                <div class="card-body">
                    <p>Meer dan 2 maanden terugkeren? <br />Dat gaat, maar je kan ze niet invullen!</p>
                </div>
            <?php endif; ?>
            <?php if ($blockFutureRegistrations) : ?>
                <div class="card-body">
                    <p>Verdere datum invullen? <br />Dat gaat niet!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let calendarId = "<?= Core\Page::id('cal'); ?>";
</script>