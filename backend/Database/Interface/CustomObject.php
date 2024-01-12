<?php

namespace Database\Interface;

use Ouzo\Utilities\Arrays;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

class CustomObject extends stdClass
{
    protected $objectAttributes = [];
    protected $encodeAttributes = [];
    protected $nl2br = [];
    protected $linkObjects = true;

    public function __construct($attributes = [], $encode = false, $nl2br = true)
    {
        foreach ($this->objectAttributes as $objAtt) {
            $this->$objAtt = Arrays::getValue($attributes, $objAtt, null);
        }

        $this->encode();
        if ($nl2br) $this->nl2br();
        $this->init();
    }

    public function encode()
    {
        foreach ($this->encodeAttributes as $encode) $this->$encode = mb_convert_encoding($this->$encode, 'UTF-8', mb_list_encodings());
    }

    public function nl2br()
    {
        foreach ($this->nl2br as $nl) $this->$nl = nl2br($this->$nl);
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
