<?php

namespace Informat\Interface;

use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

class CustomObject extends stdClass
{
    const TYPE_INTEGER = "int";
    const TYPE_DOUBLE = "double";
    const TYPE_STRING = "string";
    const TYPE_DATE = "date";
    const TYPE_TIME = "time";
    const TYPE_DATETIME = "datetime";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_JSON = "json";
    const TYPE_URL = "url";
    const TYPE_LIST = "list";
    const TYPE_GUID = "guid";
    const TYPE_ALL = "*";
    const TYPE_ARRAY = "array";

    protected $objectAttributes = [];
    protected $encodeAttributes = [];
    protected $decodeAttributes = [];
    protected $linkedAttributes = [];

    public $mapped = null;
    public $linked = null;
    public $formatted = null;

    public function __construct($attributes = [])
    {
        $this->mapped = new stdClass;
        $this->linked = new stdClass;
        $this->formatted = new stdClass;

        $this->formatted->badge = new stdClass;
        $this->formatted->icon = new stdClass;

        $this->createAttributes($attributes);
        $this->encode();
        $this->decode();
        $this->link();
        $this->init();
    }

    public function reinit()
    {
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

    protected function decode()
    {
        foreach ($this->decodeAttributes as $decode) $this->$decode = mb_convert_encoding($this->$decode, 'ISO-8859-1', 'UTF-8');
    }

    protected function link()
    {
        if (!count($this->linkedAttributes)) return;

        foreach ($this->linkedAttributes as $la => $prop) {
            $attribute = key($prop);
            if (is_null($this->$attribute) || Strings::isBlank($this->$attribute)) continue;

            $repo = $prop[$attribute];

            if (Strings::contains($this->$attribute, ";")) {
                $this->linked->$la = [];

                foreach (explode(";", $this->$attribute) as $a) {
                    $this->linked->$la[] = (new $repo)->getById($a);
                }
            } else $this->linked->$la = (new $repo)->getById($this->$attribute);
        }
    }

    public function init() {}

    public function fillWithPostData($fields = [])
    {
        foreach ($this->getKeys() as $key) $this->$key = $fields[$key] ?? \Router\Helpers::input()->post($key)?->getValue() ?? $this->$key;
    }

    public function toArray($flatten = false)
    {
        return $flatten ? Arrays::flattenKeysRecursively(General::object_to_array($this)) : General::object_to_array($this);
    }

    public function toSearchArray()
    {
        $arr = [];

        foreach ($this as $key => $value) $arr[] = $value;

        return Arrays::flatten($arr);
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
