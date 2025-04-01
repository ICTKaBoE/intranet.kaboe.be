<?php

namespace Security;

use Ouzo\Utilities\Arrays;

abstract class Session
{
    static public function accross()
    {
        session_set_cookie_params(0, "/", ".kaboe.be");
    }

    static public function start()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
    }

    static public function stop()
    {
        session_destroy();
        session_abort();
    }

    static public function set($key, $value)
    {
        self::start();
        Arrays::setNestedValue($_SESSION, [$key], $value);
    }

    static public function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    static public function get($key)
    {
        self::start();
        return Arrays::getValue($_SESSION, $key);
    }
}
