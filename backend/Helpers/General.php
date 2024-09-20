<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
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
        else if ($type == "binary") $value = Arrays::map(str_split($value), fn($v) => intval($v));
        else if ($type == "object") $value = self::convertToObject($value);
        else if ($type == "array") $value = self::arrayToObjects($value);
        else if ($type == "date") $value = Clock::at($value)->format("Y-m-d");
        else if ($type == "datetime") $value = Clock::at($value)->format("Y-m-d H:i:s");
        else if ($type == "json") $value = json_decode($value, true);

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

    static public function normalizeArray(array $items, $delimiter = '.')
    {
        $new = array();
        foreach ($items as $key => $value) {
            if (strpos($key, $delimiter) === false) {
                $new[$key] = is_array($value) ? self::normalizeArray($value, $delimiter) : $value;
                continue;
            }

            $segments = explode($delimiter, $key);
            $last = &$new[$segments[0]];
            if (!is_null($last) && !is_array($last)) {
                throw new \LogicException(sprintf("The '%s' key has already been defined as being '%s'", $segments[0], gettype($last)));
            }

            foreach ($segments as $k => $segment) {
                if ($k != 0) {
                    $last = &$last[$segment];
                }
            }
            $last = is_array($value) ? self::normalizeArray($value, $delimiter) : $value;
        }
        return $new;
    }
}
