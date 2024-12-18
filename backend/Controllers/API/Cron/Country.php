<?php

namespace Controllers\API\Cron;

use Database\Object\Country as ObjectCountry;
use Database\Repository\Country as RepositoryCountry;
use Database\Repository\Setting;
use GuzzleHttp\Client;

abstract class Country
{
    static public function Import()
    {
        $general = self::General();
        $niscodes = self::NisCodes();

        return ($general && $niscodes);
    }

    static private function General()
    {
        $apiKey = (new Setting)->get("countryapi.key")[0]->value;
        $apiEndpoint = (new Setting)->get("countryapi.endpoint")[0]->value;

        $api = trim(str_replace("{{key}}", $apiKey, $apiEndpoint));
        $result = (new Client)->request('GET', $api);

        if (!$result) return false;

        $result = json_decode($result->getBody());
        $repo = new RepositoryCountry;
        foreach ($result as $c) {
            $country = $repo->getByAlpha2Code($c->alpha2Code) ?? $repo->getByAlpha3Code($c->alpha3Code) ?? (new ObjectCountry);
            $country->alpha2Code = $c->alpha2Code;
            $country->alpha3Code = $c->alpha3Code;
            $country->cioc = $c->cioc;
            $country->numericCode = $c->numericCode;
            $country->callingCode = $c->callingCode;
            $country->officialName = $c->official_name;
            $country->translatedName = $c->translations->nld;

            $repo->set($country);
        }
    }

    static private function NisCodes()
    {
        $apiEndpoint = (new Setting)->get("niscode.endpoint")[0]->value;
        $result = (new Client)->request('GET', $apiEndpoint);

        if (!$result) return false;

        $result = json_decode($result->getBody())->items;
        $repo = new RepositoryCountry;

        foreach ($result as $c) {
            $country = $repo->getByAlpha2Code($c->isoAlpha2Code) ?? $repo->getByAlpha3Code($c->isoAlpha3Code);
            if (is_null($country)) continue;

            $country->nisCode = $c->nisCode;
            $repo->set($country);
        }
    }
}
