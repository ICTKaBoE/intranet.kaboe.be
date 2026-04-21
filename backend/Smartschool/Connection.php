<?php

namespace Smartschool;

use Database\Repository\Source;
use SoapClient;

abstract class Connection
{
    const SOURCE_ID = "smartschool";

    static public function init($sourceId, $timeout = 300)
    {
        ini_set('default_socket_timeout', $timeout);
        return new SoapClient(self::GetHost($sourceId), ['cache_wsdl' => WSDL_CACHE_NONE]);
    }

    static public function GetHost($sourceId)
    {
        return (new Source)->getById($sourceId)->host;
    }

    static public function GetPassword($sourceId)
    {
        return (new Source)->getById($sourceId)->password;
    }
}
