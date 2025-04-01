<?php

use Database\Database;
use Ouzo\Utilities\Arrays;
use Router\Helpers;
use Security\Code;

require_once "./backend/autoload.php";
Code::noTimeLimit();

ob_end_flush();
ob_implicit_flush();

$version = Helpers::url()->getParam("v");
$file = file_get_contents("./sql/v{$version}.sql");
$file = preg_replace('/^--.+$/m', "", $file);
$lines = explode(";", $file);
$lines = Arrays::map($lines, fn($l) => trim($l));
$lines = Arrays::filterNotBlank($lines);
$lines = Arrays::map($lines, fn($l) => "{$l};");
die(var_dump($lines));

$db = Database::getInstance();
$connection = $db->getConnection();

$db->beginTransaction();

try {
    foreach ($lines as $line) {
        echo "Executing line: {$line}<br />";
        $stmt = $connection->prepare($line);
        $stmt->execute();
        sleep(1);
    }

    $db->commit();
} catch (\Exception $e) {
    $db->rollback();
    echo "Failed at line:<br />{$line}<br /><br />Error Message:<br />{$e->getMessage()}";
}
