<?php

use Core\Config;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];


$settings['o365'] = Arrays::flattenKeysRecursively(Config::get("o365", override: false));
Arrays::setNestedValue($return, ['fields'], Arrays::flattenKeysRecursively($settings));

echo Json::safeEncode($return);
