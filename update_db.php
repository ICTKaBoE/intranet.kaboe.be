<?php

use Database\Database;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\Code;
use Security\FileSystem;

require_once "./backend/autoload.php";
Code::noTimeLimit();

ob_end_flush();
ob_implicit_flush();

$version = Helpers::url()->getParam("v");
$file = FileSystem::GetContent("./sql/v{$version}.sql");
$file = preg_replace('/^--.+$/m', "", $file);
$lines = explode(";", $file);
$lines = Arrays::map($lines, fn($l) => Strings::trimToNull($l));
$lines = Arrays::filterNotBlank($lines);
$lines = Arrays::map($lines, fn($l) => "{$l};");

$db = Database::getInstance();
$connection = $db->getConnection();

$db->beginTransaction();

try {
    foreach ($lines as $line) {
        echo $line;
        $stmt = $connection->prepare($line);
        $stmt->execute();
        echo "<span style='color: green'>OK</span><br />";
        sleep(5);
    }

    $db->commit();
    echo "<br />";
    echo "Committed!<br />";
    echo "Removing directory...<br />";
    foreach (FileSystem::getFiles(LOCATION_ROOT . "/sql/*") as $file) FileSystem::RemoveFile(LOCATION_ROOT . "/sql/" . basename($file));
    FileSystem::RemoveDirectory("./sql");
    echo "DONE!";
} catch (\Exception $e) {
    sleep(5);
    echo "<span style='color: red'>FAIL</span><br />";
    $db->rollback();
    echo "<br />Error Message:<br />{$e->getMessage()}";
}
