<?php

namespace Database\Object\Order;

use Security\CustomObject;

class Supplier extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "name" => self::TYPE_STRING,
        "contactName" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "phone" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $this->formatted->contactWithName = ($this->contactName ? "{$this->contactName} ($this->name)" : $this->name);
    }
}
