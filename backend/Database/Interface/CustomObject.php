<?php

namespace Database\Interface;

use Helpers\General;
use Ouzo\Utilities\Arrays;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

class CustomObject extends stdClass
{
    protected $objectAttributes = [];
    protected $encodeAttributes = [];
    protected $linkedAttributes = [];

    public $mapped = null;
    public $linked = null;
    public $formatted = null;

    public function __construct($attributes = [])
    {
        $this->mapped = new stdClass;
        $this->linked = new stdClass;
        $this->formatted = new stdClass;

        $this->createAttributes($attributes);
        $this->encode();
        $this->link();
        $this->init();
    }

    protected function createAttributes($attributes = [])
    {
        foreach ($this->objectAttributes as $objKey => $objType) {
            $objValue = Arrays::getValue($attributes, $objKey, null);
            $objValue = General::convert($objValue, $objType);
            $this->$objKey = $objValue;
        }
    }

    protected function encode()
    {
        foreach ($this->encodeAttributes as $encode) $this->$encode = mb_convert_encoding($this->$encode, 'UTF-8', mb_list_encodings());
    }

    protected function link()
    {
        if (!count($this->linkedAttributes)) return;

        foreach ($this->linkedAttributes as $la => $prop) {
            $attribute = key($prop);
            $repo = $prop[$attribute];
            $this->linked->$la = Arrays::firstOrNull((new $repo)->get($this->$attribute));
        }
    }

    public function init() {}

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
