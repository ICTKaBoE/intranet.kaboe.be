<?php

namespace Informat\SOAP\Interface;

use stdClass;
use Helpers\General;
use ReflectionObject;
use ReflectionProperty;
use Ouzo\Utilities\Arrays;

class CustomObject extends stdClass
{
    protected $objectAttributes = [];
    protected $encodeAttributes = [];

    public function __construct($attributes = [])
    {
        foreach ($this->objectAttributes as $objKey => $objType) {
            $objValue = Arrays::getValue($attributes, $objKey, null);
            $objValue = General::convert($objValue, $objType);
            $this->$objKey = $objValue;
        }

        $this->encode();
        $this->init();
    }

    public function encode()
    {
        foreach ($this->encodeAttributes as $encode) $this->$encode = utf8_encode($this->$encode);
    }

    public function init() {}

    public function toArray($flatten = false)
    {
        return $flatten ? Arrays::flattenKeysRecursively(General::object_to_array($this)) : General::object_to_array($this);
    }

    public function toSqlArray()
    {
        $array = [];
        foreach ($this->objectAttributes as $objKey => $objType) {
            $array[$objKey] = General::deconvert($this->$objKey, $objType);
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
