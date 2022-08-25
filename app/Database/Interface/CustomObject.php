<?php

namespace Database\Interface;

class CustomObject
{
    protected $objectAttributes = [];
    public function __construct($attributes = [])
    {
        foreach ($this->objectAttributes as $objAtt) {
            if (key_exists($objAtt, $attributes)) $this->$objAtt = $attributes[$objAtt];
            else $this->$objAtt = null;
        }

        $this->init();
    }

    public function init()
    {
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->objectAttributes as $objAtt) $array[$objAtt] = $this->$objAtt;

        return $array;
    }
}
