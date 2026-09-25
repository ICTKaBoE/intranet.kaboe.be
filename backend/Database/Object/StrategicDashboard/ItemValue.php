<?php

namespace Database\Object\StrategicDashboard;

use Helpers\CString;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use Security\Input;

class ItemValue extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "itemId" => self::TYPE_INTEGER,
        "value" => self::TYPE_ALL,
        "datetime" => self::TYPE_DATETIME,
        "editedByUserId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "item" => ["itemId" => \Database\Repository\StrategicDashboard\Item::class]
    ];

    public function init()
    {
        $this->calculated = Input::check($this->value, Input::INPUT_TYPE_INT) || Input::check($this->value, Input::INPUT_TYPE_FLOAT) ? $this->value : 0;

        if (!Input::check($this->value, Input::INPUT_TYPE_INT) && !Input::check($this->value, Input::INPUT_TYPE_FLOAT)) {
            $values = json_decode($this->value, true) ?: [];
            foreach ($values as $k => $v) $this->calculated += $v;

            foreach ($values as $k => $v) {
                $n = "value_{$k}";
                $this->$n = $v;
            }
        }

        $this->formatted->value = (Strings::equal($this->linked->item->linked->type->short, "number") && is_numeric($this->value)) ? CString::formatNumber($this->value, $this->linked->item->valueRound) : $this->value;
        if ($this->linked->item->valuePrefix) $this->formatted->value = "{$this->linked->item->valuePrefix} {$this->formatted->value}";
        if ($this->linked->item->valueSuffix) $this->formatted->value = "{$this->formatted->value} {$this->linked->item->valueSuffix}";
    }
}
