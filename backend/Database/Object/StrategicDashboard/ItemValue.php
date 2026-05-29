<?php

namespace Database\Object\StrategicDashboard;

use Helpers\CString;
use Security\CustomObject;

class ItemValue extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "itemId" => self::TYPE_INTEGER,
        "value" => self::TYPE_ALL,
        "datetime" => self::TYPE_DATETIME,
        "editedByUserId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $this->formatted->value = is_int($this->value) ? CString::formatNumber($this->value, 2) : $this->value;
    }
}
