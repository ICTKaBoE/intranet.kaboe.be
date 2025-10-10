<?php

namespace Database\Object\Helpdesk;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class Category extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_STRING,
        "categoryId" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER
    ];

    public function init()
    {
        $this->formatted->id = $this->categoryId ? "{$this->categoryId}-{$this->id}" : $this->id;
    }
}
