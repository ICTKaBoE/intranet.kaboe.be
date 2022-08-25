<?php

use Core\Config;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];


$settings['site'] = Arrays::flattenKeysRecursively(Config::get("site", override: false));
$settings['page'] = Arrays::flattenKeysRecursively(Config::get("page", override: false));
Arrays::setNestedValue($return, ['fields'], Arrays::flattenKeysRecursively($settings));

echo Json::safeEncode($return);
