<?php

namespace Database\Object\Helpdesk;

use Security\CustomObject;
use Ouzo\Utilities\Clock;

class Category extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "categoryId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "managementType" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER
    ];

    protected $linkedAttributes = [
        "category" => ["categoryId" => \Database\Repository\Helpdesk\Category::class]
    ];

    public function init()
    {
        $this->formatted->name = ($this->categoryId ? "{$this->linked->category->name} - " : "") . $this->name;
    }
}
