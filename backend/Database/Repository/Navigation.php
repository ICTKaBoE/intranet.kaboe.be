<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Navigation extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_navigation", \Database\Object\Navigation::class, guidField: false);
    }

    public function getByParentId($parentId)
    {
        $statement = $this->prepareSelect();
        $statement->where('parentId', $parentId);

        return $this->executeSelect($statement);
    }

    public function getByLink($link)
    {
        $statement = $this->prepareSelect();
        $statement->where('link', $link);

        return $this->executeSelect($statement);
    }

    public function getByParentIdAndLink($parentId, $link)
    {
        $statement = $this->prepareSelect();
        $statement->where('parentId', $parentId);
        $statement->where('link', $link);

        return $this->executeSelect($statement);
    }
}
