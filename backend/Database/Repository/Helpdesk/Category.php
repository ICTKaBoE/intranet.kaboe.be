<?php

namespace Database\Repository\Helpdesk;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Category extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_helpdesk_category", \Database\Object\Helpdesk\Category::class, deletedField: false, guidField: false);
    }

    public function getMainCategoryOnly()
    {
        $statement = $this->prepareSelect();
        return Arrays::filter($this->executeSelect($statement), fn($i) => is_null($i->categoryId));
    }

    public function getByCategoryId($categoryId)
    {
        $statement = $this->prepareSelect(filters: ["categoryId" => $categoryId]);
        return $this->executeSelect($statement);
    }

    public function getByIdAndCategoryId($id, $categoryId)
    {
        $statement = $this->prepareSelect(filters: [
            "id" => $id,
            "categoryId" => $categoryId
        ]);

        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
