<?php

namespace Helpers;

use stdClass;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;

abstract class General
{
    static public function convert($value, $type)
    {
        if (is_null($value) && $type !== "json") return $value;
        else if ($type == "int") $value = intval($value);
        else if ($type == "string") $value = (string)$value;
        else if ($type == "bool" || $type == "boolean") $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        else if ($type == "list") $value = explode(PHP_EOL, $value);
        else if ($type == "binary") $value = Arrays::map(str_split($value), fn($v) => intval($v));
        else if ($type == "object") $value = self::convertToObject($value);
        else if ($type == "array") $value = self::arrayToObjects($value);
        else if ($type == "date") $value = Clock::at($value)->format("Y-m-d");
        else if ($type == "datetime") $value = Clock::at($value)->format("Y-m-d H:i:s");
        else if ($type == "json") $value = json_decode($value ?: "{}", true);
        else if ($type == "base64") $value = base64_decode($value);

        return $value;
    }

    static public function deconvert($value, $origType)
    {
        if (is_null($value)) return $value;
        else if ($origType == "list") $value = implode(PHP_EOL, $value);
        else if ($origType == "binary") $value = implode("", $value);
        else if ($origType == "json") $value = json_encode($value);

        if ($origType == "json" && empty($value)) $value = null;

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

    static public function object_to_array($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = (is_array($value) || is_object($value)) ? self::object_to_array($value) : $value;
        }
        return $result;
    }

    static public function filter(&$items, $filters)
    {
        if (empty($filters)) return $items;

        foreach ($filters as $key => $value) {
            if (!$value || empty($value)) continue;

            if (is_array($value)) $items = Arrays::filter($items, fn($i) => Arrays::contains($value, $i->$key));
            else $items = Arrays::filter($items, fn($i) => Strings::equal($i->$key, $value));
        }

        return $items;
    }

    static public function page(&$items, $page = null, $limit = null)
    {
        if (!$page) $page = Helpers::url()->getParam("page", 0);
        if (!$limit) $limit = Helpers::url()->getParam("limit");

        if ($page || $limit) {
            $start = $page * $limit;
            $items = array_slice($items, $start, $limit);

            return $items;
        }

        return $items;
    }

    static public function hasNextPage($items, $page = null, $limit = null)
    {
        if (!$page) $page = Helpers::url()->getParam("page", 0);
        if (!$limit) $limit = Helpers::url()->getParam("limit");

        if ($page || $limit) {
            $start = $page * $limit;
            $items = array_slice($items, $start);
            return (count($items) > $limit);
        }

        return false;
    }

    static public function processTemplate($items = [], $template = null, $searchPrePost = "@")
    {
        if (!$template) $template = Helpers::url()->getParam("template");
        $output = "";

        foreach ($items as $i) {
            $t = $template;
            foreach ($i as $key => $value) $t = str_replace("{$searchPrePost}{$key}{$searchPrePost}", $value, $t);
            $output .= $t;
        }

        return preg_replace("/$searchPrePost.*?$searchPrePost/", "", $output);
    }

    static public function removeLeadingZero($string)
    {
        return preg_replace('/^0+/', '', $string);
    }
}
