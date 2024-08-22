<?php

namespace Informat\Interface;

use Helpers\General;
use Ouzo\Utilities\Arrays;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

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
        foreach ($this->encodeAttributes as $encode) $this->$encode = mb_convert_encoding($this->$encode, 'UTF-8', mb_list_encodings());
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

    public function toSqlArray()
    {
        $array = [];
        foreach ($this->objectAttributes as $objAtt => $objExtra) {
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
