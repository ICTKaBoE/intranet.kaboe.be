<?php

namespace Database\Repository\Library;

use Database\Interface\Repository;

class Book extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_library_book", \Database\Object\Library\Book::class, orderField: 'title');
    }

    public function getByAuthorId($authorId)
    {
        $statement = $this->prepareSelect();
        $statement->where("authorId", $authorId);

        return $this->executeSelect($statement);
    }

    public function getByCategoryId($categoryId)
    {
        $statement = $this->prepareSelect();
        $statement->where("categoryId", $categoryId);

        return $this->executeSelect($statement);
    }
}
