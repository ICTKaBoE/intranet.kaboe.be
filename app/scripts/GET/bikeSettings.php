<?php

use Core\Config;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];

$settings = Arrays::flattenKeysRecursively(Config::get("tool/bike"));
Arrays::setNestedValue($return, ['fields'], $settings);

echo Json::safeEncode($return);
