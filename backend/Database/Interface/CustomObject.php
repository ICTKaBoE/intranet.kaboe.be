<?php

namespace Database\Interface;

use Ouzo\Utilities\Arrays;
use ReflectionObject;
use ReflectionProperty;

class CustomObject
{
    protected $objectAttributes = [];
    protected $encodeAttributes = [];
    protected $linkObjects = true;

    public function __construct($attributes = [], $encode = false)
    {
        foreach ($this->objectAttributes as $objAtt) {
            $this->$objAtt = Arrays::getValue($attributes, $objAtt, null);
        }

        $this->encode();
        $this->init();
    }

    public function encode()
    {
        foreach ($this->encodeAttributes as $encode) $this->$encode = utf8_encode($this->$encode);
    }

    public function init()
    {
    }

    public function link()
    {
        return $this;
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

    public function toSqlArray()
    {
        $array = [];
        foreach ($this->objectAttributes as $objAtt) {
            $array[$objAtt] = $this->$objAtt;
        }

        return $array;
    }

    public function getKeys()
    {
        $array = [];
        $reflect = new ReflectionObject($this);

        foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $array[] = $prop->getName();
        }

        return $array;
    }
}
