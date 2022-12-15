<?php

use Router\Router;

require_once __DIR__ . "/backend/autoload.php";

if ($_SERVER['REQUEST_URI'] === "/") {
	header("Location: " . ROUTER_DEFAULT_PREFIX);
	exit();
}

Router::start();
