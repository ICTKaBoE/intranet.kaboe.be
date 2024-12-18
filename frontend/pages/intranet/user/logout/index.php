<?php

use Router\Helpers;
use Security\Session;

Session::start();
Session::stop();

header('Location: ' . (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost());
exit();
