<?php

namespace Database\Repository\Navigation;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class Setting extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_navigation_setting", \Database\Object\Navigation\Setting::class, orderField: "key", deletedField: false, guidField: false);
    }

    public function getByNavigationId($navigationId)
    {
        $statement = $this->prepareSelect(filters: ['navigationId' => $navigationId]);
        return $this->executeSelect($statement);
    }

    public function getByNavigationIdAndKey($navigationId, $key)
    {
        $statement = $this->prepareSelect(filters: ['navigationId' => $navigationId, "key" => $key]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
