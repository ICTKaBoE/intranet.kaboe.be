<?php

use Core\Config;
use Ouzo\Utilities\Json;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];

try {
    foreach ($_POST as $key => $value) Config::set(explode("_", $key), $value);

    $return['message']['state'] = "success";
    $return['message']['content'] = "Instellingen opgeslagen!";
    $return['message']['disappear'] = 5;
} catch (\Exception $e) {
    $return['message']['state'] = 'error';
    $return['message']['content'] = $e->getMessage();
}

echo Json::safeEncode($return);
