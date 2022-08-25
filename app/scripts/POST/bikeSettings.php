<?php

use Core\Config;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];

$settings = Config::get("tool/bike");

if (isset($_POST['colorDistance1'])) $settings['colorDistance1'] = $_POST['colorDistance1'];
if (isset($_POST['colorDistance2'])) $settings['colorDistance2'] = $_POST['colorDistance2'];
if (isset($_POST['lastPayDate'])) $settings['lastPayDate'] = $_POST['lastPayDate'];
$settings['blockPastRegistration'] = isset($_POST['blockPastRegistration']);
$settings['blockFutureRegistration'] = isset($_POST['blockFutureRegistration']);

try {
    Config::set("tool/bike", $settings);

    $return['message']['state'] = "success";
    $return['message']['content'] = "Profiel opgeslagen!";
    $return['message']['disappear'] = 5;
} catch (\Exception $e) {
    $return['message']['state'] = 'error';
    $return['message']['content'] = $e->getMessage();
}

echo Json::safeEncode($return);
