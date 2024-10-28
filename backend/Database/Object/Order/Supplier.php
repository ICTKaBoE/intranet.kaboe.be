<?php

namespace Database\Object\Order;

use Database\Interface\CustomObject;

class Supplier extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "name" => "string",
        "contactName" => "string",
        "email" => "string",
        "phone" => "string",
        "deleted" => "boolean"
    ];
}
