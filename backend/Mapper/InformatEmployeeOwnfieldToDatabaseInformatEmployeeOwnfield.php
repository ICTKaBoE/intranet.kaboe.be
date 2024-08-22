<?php

namespace Mapper;

class InformatEmployeeOwnfieldToDatabaseInformatEmployeeOwnfield extends MapperInterface
{
    protected $mapFields = [
        "vvId" => "informatId",
        "naam" => "name",
        "waarde" => "value"
    ];
}
