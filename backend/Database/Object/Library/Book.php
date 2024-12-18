<?php

namespace Database\Object\Library;

use Database\Interface\CustomObject;

class Book extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "authorId" => "int",
        "categoryId" => "int",
        "amount" => "int",
        "free" => "int",
        "title" => "string",
        "isdn" => "string",
        "lendTo" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School::class],
        "author" => ['authorId' => \Database\Repository\Library\Author::class],
        "category" => ["categoryId" => \Database\Repository\Library\Category::class]
    ];

    public function init()
    {
        $this->formatted->free = "{$this->free}/{$this->amount}";
        $this->formatted->nameWithAuthor = "{$this->linked->author->name} - {$this->title}";
        $this->formatted->nameWithAuthorAndCategory = "{$this->formatted->nameWithAuthor} ({$this->linked->category->name})";
    }
}
