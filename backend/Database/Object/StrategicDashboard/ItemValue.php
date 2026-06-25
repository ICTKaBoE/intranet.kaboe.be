<?php

namespace Database\Object\StrategicDashboard;

use Helpers\CString;
use Helpers\General;
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

        $this->formatted->value = is_int($this->value) ? CString::formatNumber($this->value, 2) : $this->value;
    }
}
