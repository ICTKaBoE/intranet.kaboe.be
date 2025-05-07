<?php

require_once __DIR__ . "/backend/autoload.php";

Security\Session::accross();
Security\Session::start();
Security\Code::errors(false);

Router\Router::start();
