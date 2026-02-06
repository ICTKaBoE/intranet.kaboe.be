<?php

namespace Smartschool\Interface;

use Exception;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use stdClass;
use Smartschool\Connection;

class Repository extends stdClass
{
    const OUTPUT_RAW = "raw";
    const OUTPUT_XML = "xml";
    const OUTPUT_ARRAY = "array";
    const OUTPUT_BASE64 = "b64";

    public function __construct($sourceId, $function, $object, $output = self::OUTPUT_RAW, $outputRemoveKeys = [])
    {
        $this->function = $function;
        $this->object = $object;
        $this->output = $output;
        $this->outputRemoveKeys = $outputRemoveKeys;

        $this->client = Connection::init($sourceId);
        $this->clientPassword = Connection::GetPassword($sourceId);
    }

    public function get(...$params)
    {
        try {
            $function = $this->function;
            $output = $this->client->$function($this->clientPassword, ...$params);
            $result = null;

            if (is_int($output)) throw new Exception("Failed: {$function}");
            else {
                if (is_array($this->output)) {
                    foreach ($this->output as $o) $output = $this->convertOutput($output, $o);
                    $result = $output;
                } else  $result = $this->convertOutput($output, $this->output);

                return $result;
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    private function convertOutput($output, $outputType)
    {
        $return = null;

        if (Strings::equal($outputType, self::OUTPUT_XML)) {
            $array = General::xmlToArray($output);
            if ($this->outputRemoveKeys) $array = Arrays::getNestedValue($array, $this->outputRemoveKeys);

            $return = $array;
        } else if (Strings::equal($outputType, self::OUTPUT_ARRAY)) $return = $this->convertToObjects($output);
        else if (Strings::equal($outputType, self::OUTPUT_BASE64)) $return = base64_decode($output);
        else if (Strings::equal($outputType, self::OUTPUT_RAW)) $return = $output;

        return $return;
    }

    private function convertToObjects($result)
    {
        if (!$result) return [];

        $objects = [];
        foreach ($result as $row) $objects[] = new $this->object($row);
        return $objects;
    }
}
