<?php

namespace Mapper;

use stdClass;

class MapperInterface extends stdClass
{
    protected $mapFields = [];

    public function map($sourceObject, $destinationObject, $defaultValue = null)
    {
        $sourceObject = $this->format($sourceObject);

        foreach ($this->mapFields as $sourceKey => $destinationKey) {
            $destinationObject->$destinationKey = $sourceObject->$sourceKey ?? $defaultValue;
        }

        return $destinationObject;
    }

    public function format($sourceObject)
    {
        return $sourceObject;
    }
}
