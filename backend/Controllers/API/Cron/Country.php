<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use GuzzleHttp\Client;
use Ouzo\Utilities\Clock;
use Database\Repository\Setting\Setting;
use Database\Repository\General\Language;
use Database\Repository\General\Nationality;
use Database\Object\General\Country as ObjectCountry;
use Database\Object\General\Language as ObjectLanguage;
use Database\Object\General\Nationality as ObjectNationality;
use Database\Repository\General\Country as RepositoryCountry;

abstract class Country
{
    static public function Import()
    {
        $general = self::General();
        $niscodes = self::NisCodes();
        $nationalities = self::Nationalities();
        $languages = self::Languages();

        return ($general && $niscodes && $nationalities && $languages);
    }

    static private function General()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Importing countries...");
        $apiKey = (new Setting)->get("countryapi.key")[0]->value;
        $apiEndpoint = (new Setting)->get("countryapi.endpoint")[0]->value;

        $api = trim(str_replace("{{key}}", $apiKey, $apiEndpoint));
        $result = (new Client)->request('GET', $api);

        if (!$result) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "Failed!");
            return false;
        }

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
            $country->name = $c->translations->nld;

            $repo->set($country);
        }

        return true;
    }

    static private function NisCodes()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Importing Nis Codes...");
        $apiEndpoint = (new Setting)->get("niscode.endpoint")[0]->value;
        $result = (new Client)->request('GET', $apiEndpoint);

        if (!$result) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "Failed!");
            return false;
        }

        $result = json_decode($result->getBody())->items;
        $repo = new RepositoryCountry;

        foreach ($result as $c) {
            $country = $repo->getByAlpha2Code($c->isoAlpha2Code) ?? $repo->getByAlpha3Code($c->isoAlpha3Code);
            if (is_null($country)) continue;

            $country->nisCode = $c->nisCode;
            $repo->set($country);
        }

        return true;
    }

    static private function Nationalities()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Importing Nationalities...");
        $repo = new Nationality;
        $nationalities = json_decode(file_get_contents("https://raw.githubusercontent.com/sourcecode911/i18n-nationality/refs/heads/master/langs/nl.json"), true);

        foreach ($nationalities["nationalities"] as $code => $name) {
            $lang = $repo->getByCode($code) ?? $repo->getByName($name) ?? (new ObjectNationality);
            $lang->code = $code;
            $lang->name = $name;

            $repo->set($lang);
        }

        return true;
    }

    static private function Languages()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Importing Languages...");
        $repo = new Language;
        $languages = json_decode(file_get_contents("https://raw.githubusercontent.com/cospired/i18n-iso-languages/refs/heads/main/langs/nl.json"), true);

        foreach ($languages["languages"] as $code => $name) {
            $lang = $repo->getByCode($code) ?? $repo->getByName($name) ?? (new ObjectLanguage);
            $lang->code = $code;
            $lang->name = $name;

            $repo->set($lang);
        }

        return true;
    }
}
