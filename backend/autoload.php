<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config/variables.php";

spl_autoload_register(function ($class) {
    $filename = __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    if (file_exists($filename)) {
        require_once $filename;

        if (class_exists($class)) return TRUE;
    }

    return FALSE;
});
