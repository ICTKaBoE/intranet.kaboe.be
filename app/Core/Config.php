<?php

namespace Core;

use Ouzo\Utilities\Arrays;
use Security\Request;

abstract class Config
{
    static public function get($key = null, $override = true)
    {
        $json = self::read();

        if ($override && file_exists(LOCATION_PUBLIC . "/pages/" . Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL) . "/override.json")) {
            $overrideJson = json_decode(file_get_contents(LOCATION_PUBLIC . "/pages/" . Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL) . "/override.json"), TRUE);
            self::override($json, $overrideJson);
        }

        return is_null($key) ? $json : Arrays::getNestedValue($json, is_array($key) ? $key : explode("/", $key));
    }

    static public function set($key, $value)
    {
        $json = self::read();
        Arrays::setNestedValue($json, is_array($key) ? $key : explode("/", $key), $value);

        self::write($json);
    }

    static private function read()
    {
        return json_decode(file_get_contents(LOCATION_APP . "/config/config.json"), TRUE);
    }

    static private function write($json)
    {
        file_put_contents(LOCATION_APP . "/config/config.json", json_encode($json));
    }

    static private function override(&$original, $overrideJson)
    {
        foreach ($overrideJson as $key => $value) {
            if (!is_array($value)) $original[$key] = $value;
            else {
                self::override($original[$key], $value);
            }
        }
    }
}
