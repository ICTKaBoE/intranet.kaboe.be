<?php

namespace Security;

use Ouzo\Utilities\Arrays;

class Request
{
    private $get = null;

    private function readGet()
    {
        if (is_null($this->get)) $this->get = $_GET;
    }

    static public function getUrl()
    {
        $url = "";
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") $url = $_SERVER['HTTP_REFERER'];
        else $url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $url = parse_url($url);

        return $url;
    }

    static public function host($protocol = null)
    {
        $url = self::getUrl();
        if (is_null($protocol)) $protocol = $url['scheme'];
        $host = $url['host'];

        return "{$protocol}://{$host}";
    }

    static public function parameter($key)
    {
        $url = self::getUrl();
        $get = [];
        parse_str(isset($url['query']) ? $url['query'] : (isset($url['fragment']) ? $url['fragment'] : ''), $get);

        return Arrays::getValue($get, $key, false);
    }

    public function setParameter($key, $value)
    {
        $this->readGet();
        Arrays::setNestedValue($this->get, [$key], $value);

        return $this;
    }

    public function removeParamter($key)
    {
        $this->readGet();
        Arrays::removeNestedKey($this->get, [$key]);

        return $this;
    }

    public function write()
    {
        $get = $this->get;
        $this->get = null;
        return $this::host() . "?" . http_build_query($get);
    }
}
