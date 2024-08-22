<?php

namespace Mapper;

class InformatEmployeeToDatabaseInformatEmployee extends MapperInterface
{
    protected $mapFields = [
        "pPersoon" => "informatId",
        "personId" => "personId",
        "naam" => "name",
        "voornaam" => "firstName",
        "bijkomendeVoornamen" => "extraFirstName",
        "stamnr" => "baseNumber",
        "geslacht" => "sex",
        "geboortedatum" => "birthdate",
        "geboorteplaats" => "birthplace",
        "geboortelandCode" => "birthCountryNISCode",
        "nationaliteitCode" => "nationalityNISCode",
        "rijksregisternr" => "nin",
        "bisnr" => "bis",
        "bankIban" => "bankAccount",
        "bankBic" => "bankBic",
        "isActive" => "active"
    ];

    public function format($sourceObject)
    {
        $sourceObject->bankIban = $sourceObject?->bank?->iban ?? null;
        $sourceObject->bankBic = $sourceObject?->bank?->bic ?? null;
        return $sourceObject;
    }
}
