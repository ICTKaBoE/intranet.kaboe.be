<?php

namespace Database\Object\Library;

use Security\CustomObject;

class Book extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "authorId" => self::TYPE_INTEGER,
        "categoryId" => self::TYPE_INTEGER,
        "amount" => self::TYPE_INTEGER,
        "free" => self::TYPE_INTEGER,
        "title" => self::TYPE_STRING,
        "isdn" => self::TYPE_STRING,
        "lendTo" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ['schoolId' => \Database\Repository\School\School::class],
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
