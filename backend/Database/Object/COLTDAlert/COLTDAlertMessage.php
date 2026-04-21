<?php

namespace Database\Object\COLTDAlert;

use Ouzo\Utilities\Clock;
use Security\CustomObject;
use stdClass;

class COLTDAlertMessage extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "coltdalertId" => self::TYPE_INTEGER,
        "ringringGuid" => self::TYPE_GUID,
        "statusCode" => self::TYPE_INTEGER,
        "statusDescription" => self::TYPE_STRING,
        "to" => self::TYPE_STRING,
        "timeScheduled" => self::TYPE_DATETIME,
        "timeDelivered" => self::TYPE_DATETIME,
    ];

    public function init()
    {
        $this->formatted->timeScheduled = new stdClass;
        $this->formatted->timeScheduled->display = Clock::at($this->timeScheduled)->format("d/m/Y H:i:s");
        $this->formatted->timeScheduled->sort = Clock::at($this->timeScheduled)->format("U");

        $this->formatted->timeDelivered = new stdClass;
        $this->formatted->timeDelivered->display = $this->timeDelivered ? Clock::at($this->timeDelivered)->format("d/m/Y H:i:s") : null;
        $this->formatted->timeDelivered->sort = $this->timeDelivered ? Clock::at($this->timeDelivered)->format("U") : null;

        $this->formatted->status = "{$this->statusDescription} - {$this->statusCode}";
    }
}
