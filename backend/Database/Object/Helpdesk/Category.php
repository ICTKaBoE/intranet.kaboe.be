<?php

namespace Database\Object\Helpdesk;

use Database\Interface\CustomObject;
use Ouzo\Utilities\Clock;

class Category extends CustomObject
{
    protected $objectAttributes = [
        "id" => "string",
        "categoryId" => "string",
        "name" => "string",
        "order" => "int"
    ];

    public function init()
    {
        $this->formatted->id = $this->categoryId ? "{$this->categoryId}-{$this->id}" : $this->id;
    }
}
