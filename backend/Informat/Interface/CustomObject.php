<?php

namespace Informat\Interface;

use Ouzo\Utilities\Arrays;

class CustomObject
{
    protected $objectAttributes = [];
    protected $encodeAttributes = [];

    public function __construct($attributes = [], $encode = false)
    {
        foreach ($this->objectAttributes as $objAtt) {
            $this->$objAtt = Arrays::getValue($attributes, $objAtt, null);
        }

        if ($encode) $this->encode();
        $this->init();
    }

    public function encode()
    {
        foreach ($this->encodeAttributes as $encode) $this->$encode = utf8_encode($this->$encode);
    }

    public function init()
    {
    }

    public function toArray()
    {
        $array = [];
        foreach ($this as $key => $value) {
            if (is_array($value) || is_object($value)) continue;

            $array[$key] = $value;
        }

        return $array;
    }
}
