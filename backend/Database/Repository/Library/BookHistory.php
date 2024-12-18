<?php

namespace Database\Repository\Library;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class BookHistory extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_library_book_history", \Database\Object\Library\BookHistory::class, orderField: 'lendDateTime', deletedField: false, guidField: false);
    }

    public function getByBookId($bookId)
    {
        $statement = $this->prepareSelect();
        $statement->where("bookId", $bookId);

        return $this->executeSelect($statement);
    }

    public function getNotReturnedByBookId($bookId)
    {
        $statement = $this->prepareSelect();
        $statement->where('bookId', $bookId);

        $items = $this->executeSelect($statement);
        return Arrays::filter($items, fn($i) => Strings::isBlank($i->receiverUserId));
    }
}
