<?php

namespace CloudMersive;

use Database\Repository\Setting;
use GuzzleHttp\Client;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\FileSystem;
use Swagger\Client\Api\ConvertDocumentApi;
use Swagger\Client\Configuration;

class Convert
{
    public function __construct()
    {
        $this->enabled = General::convert((new Setting)->get("cloudmersive.enabled")[0]->value, "boolean");
    }

    private function createInstance()
    {
        if (!$this->enabled) return false;

        $apiKey = (new Setting)->get("cloudmersive.api.key")[0]->value;
        $config = Configuration::getDefaultConfiguration()->setApiKey('Apikey', $apiKey);
        $this->apiInstance = new ConvertDocumentApi(new Client, $config);

        return true;
    }

    public function convert($file, $outputFile)
    {
        if (!$this->createInstance()) return;

        $ext = Arrays::last(explode(".", $file));
        $res = null;
        if ($ext == "doc") $res = $this->apiInstance->convertDocumentDocToPdf($file);
        else if ($ext == "docx") $res = $this->apiInstance->convertDocumentDocxToPdf($file);

        if (Strings::startsWith($res, "%PDF")) return FileSystem::WriteFile($outputFile, $res);
        return false;
    }
}
