<?php

namespace Database\Object\Accident;

use Security\CustomObject;

class Location extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "categoryId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "extendedOptions" => self::TYPE_BOOLEAN,
        "order" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "category" => ["categoryId" => \Database\Repository\Accident\Location::class]
    ];

    public function init()
    {
        $this->formatted->name = ($this->categoryId ? "{$this->linked->category->name} - " : "") . $this->name;
    }
}
