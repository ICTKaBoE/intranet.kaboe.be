<?php

namespace Mapper;

class InformatEmployeeNumberToDatabaseInformatEmployeeNumber extends MapperInterface
{
    protected $mapFields = [
        "id" => "informatId",
        "type" => "type",
        "soort" => "kind",
        "nr" => "number"
    ];
}
