<?php

namespace Smartschool;

use Database\Repository\Source;
use SoapClient;

abstract class Connection
{
    const SOURCE_ID = "smartschool";

    static public function init($sourceId)
    {
        return new SoapClient(self::GetHost($sourceId));
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
