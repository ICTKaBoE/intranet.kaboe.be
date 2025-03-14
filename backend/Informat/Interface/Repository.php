<?php

namespace Informat\Interface;

use Database\Repository\Setting\Setting;
use GuzzleHttp\Client;
use stdClass;
use Informat\Connection;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class Repository extends stdClass
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    const ENDPOINT_EMPLOYEE = "https://personeelsapi.informatsoftware.be/employees/<id>/<extend>";
    const ENDPOINT_STUDENT = "https://leerlingenapi.informatsoftware.be/<version>/";
    const ENDPOINT_STUDENT_PREREGISTRATION = self::ENDPOINT_STUDENT . "preregistrations/";
    const ENDPOINT_STUDENT_REGISTRATIONS = self::ENDPOINT_STUDENT . "registrations/";
    const ENDPOINT_STUDENT_STUDENTS = self::ENDPOINT_STUDENT . "students/<id>/<extend>";

    public function __construct($endpoint, $object, $extend = null, $apiVersion = 1, $shift = null)
    {
        $this->endpoint = $endpoint;
        $this->object = $object;
        $this->extend = $extend;
        $this->apiVersion = $apiVersion;
        $this->shift = $shift;
    }

    public function get($instituteNumber, $id = null, $raw = false)
    {
        $endpoint = Path::normalize(str_replace("<version>", $this->apiVersion ?: "", $this->endpoint));
        $endpoint = Path::normalize(str_replace("<extend>", $this->extend ?: "", $endpoint));
        $endpoint = Path::normalize(str_replace("<id>", $id ?: "", $endpoint));

        $requestHeaders = [
            "InstituteNo" => $instituteNumber
        ];

        $requestQuery = [
            "schoolyear" => INFORMAT_CURRENT_SCHOOLYEAR,
            "structure" => Arrays::first((new Setting)->get("informat.structure"))->value
        ];

        $result = $this->execute($endpoint, $requestHeaders, $requestQuery);
        if ($this->shift) $result = $result[$this->shift];

        $objects = [];
        if (!$raw && $result) {
            foreach ($result as $res) $objects[] = new $this->object($res);
        }

        return $raw ? $result : $objects;
    }

    private function execute($endpoint, $requestHeaders = [], $requestQueryBody = [], $method = self::METHOD_GET)
    {
        if (Connection::init()) {
            $requestHeaders['Api-Version'] = $this->apiVersion;
            $requestHeaders["Authorization"] = Connection::GetTokenType() . " " . Connection::GetTokenValue();

            $options = [
                'headers' => $requestHeaders
            ];
            if ($method == self::METHOD_GET) $options['query'] = $requestQueryBody;
            else $options['body'] = $requestQueryBody;

            $response = (new Client())->request($method, $endpoint, $options);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        return false;
    }
}
