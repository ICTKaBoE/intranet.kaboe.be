<?php

namespace Database\Object;

use Database\Interface\CustomObject;

class UserAddress extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "userId" => "int",
        "informatId" => "string",
        "street" => "string",
        "number" => "int",
        "bus" => "string",
        "zipcode" => "string",
        "city" => "string",
        "country" => "string",
        "current" => "boolean",
        "since" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "user" => [
            "userId" => \Database\Repository\User::class
        ]
    ];

    public function init()
    {
        $this->formatted->address = "{$this->street} {$this->number}{$this->bus}, {$this->zipcode} {$this->city}";
    }
}
