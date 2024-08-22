<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;
use stdClass;

abstract class General
{
    static public function convert($value, $type)
    {
        if (is_null($value)) return $value;
        else if ($type == "int") $value = intval($value);
        else if ($type == "string") $value = (string)$value;
        else if ($type == "bool" || $type == "boolean") $value = boolval($value);
        else if ($type == "list") $value = explode(PHP_EOL, $value);
        else if ($type == "binary") $value = Arrays::map(str_split($value), fn ($v) => intval($v));
        else if ($type == "object") $value = self::convertToObject($value);
        else if ($type == "array") $value = self::arrayToObjects($value);

        return $value;
    }

    static public function convertToObject($toConvert)
    {
        return (object)$toConvert;
    }

    static public function arrayToObjects($array)
    {
        $objects = [];
        foreach ($array as $a) $objects[] = self::convertToObject($a);

        return $objects;
    }
}
