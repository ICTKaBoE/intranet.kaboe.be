<?php

use Database\Object\Navigation\Setting as NavigationSetting;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Navigation\Setting;
use Ouzo\Utilities\Arrays;

require_once "./backend/autoload.php";
Security\Code::errors(false);

$navigationWithSettings = Arrays::filter((new Navigation)->get(), fn($i) => !empty($i->settings));
$repo = new Setting;

foreach ($navigationWithSettings as $nws) {
    $settings = Arrays::flattenKeysRecursively($nws->settings);

    foreach ($settings as $key => $value) {
        $item = $repo->getByNavigationIdAndKey($nws->id, $key) ?? new NavigationSetting;
        $item->navigationId = $nws->id;
        $item->key = $key;
        $item->value = $value;

        $repo->set($item);
    }
}
