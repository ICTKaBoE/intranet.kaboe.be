<?php

namespace Database\Repository\StrategicDashboard;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class ItemValue extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_strategicdashboard_item_value", \Database\Object\StrategicDashboard\ItemValue::class, orderField: "datetime", orderDirection: self::ORDER_DIRECTION_DESC, guidField: false);
    }

    public function getLastValueByItemId($itemId)
    {
        $statement = $this->prepareSelect(filters: ['itemId' => $itemId]);
        return Arrays::firstOrNull($this->executeSelect($statement));
    }
}
