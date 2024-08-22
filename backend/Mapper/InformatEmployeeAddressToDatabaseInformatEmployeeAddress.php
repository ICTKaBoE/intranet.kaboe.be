<?php

namespace Mapper;

class InformatEmployeeAddressToDatabaseInformatEmployeeAddress extends MapperInterface
{
    protected $mapFields = [
        "id" => "informatId",
        "straat" => "street",
        "nummer" => "number",
        "bus" => "bus",
        "postcode" => "zipcode",
        "gemeente" => "city",
        "landCode" => "countryNISCode",
        "isVerblijf" => "current"
    ];
}
