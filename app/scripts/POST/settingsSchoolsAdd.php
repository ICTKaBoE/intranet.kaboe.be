<?php

use Core\Config;
use Core\Page;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];
$continue = true;

$name = $_POST['name'];
if (Strings::isBlank($name)) $continue = false;

try {
    if ($continue) {
        $schools = Config::get("schools");
        $highestIndex = 0;
        foreach ($schools as $id => $school) {
            if ($id < $highestIndex) $highestIndex = $id;
        }

        $schools[] = [$highestIndex++ => $name];
        Config::set('schools', $schools);

        $return['reset'] = true;
        $return['closeModal'] = $_POST['modalId'];
        $return['reloadTable'] = $_POST['tableId'];
    }
} catch (\Exception $e) {
    $return['message']['state'] = 'error';
    $return['message']['content'] = $e->getMessage();
}

echo Json::safeEncode($return);
