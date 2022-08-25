<?php

use Security\Request;
use Security\Session;

require_once __DIR__ . "/../../../app/autoload.php";

Session::start();
Session::stop();

header('Location: ' . Request::host());
exit();
