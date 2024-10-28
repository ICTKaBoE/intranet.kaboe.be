<?php

namespace Informat\REST\Interface;

use Database\Repository\Setting;
use GuzzleHttp\Client;
use stdClass;
use Informat\REST\Connection;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class Repository extends stdClass
{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";

    const ENDPOINT_EMPLOYEE = "https://personeelsapi.informatsoftware.be/employees/<id>/<extend>";
    const ENDPOINT_STUDENT_PREREGISTRATION = "https://leerlingenapi.informatsoftware.be/1/preregistrations/";
    const ENDPOINT_STUDENT_REGISTRATIONS = "https://leerlingenapi.informatsoftware.be/1/registrations/";
    const ENDPOINT_STUDENT_STUDENTS = "https://leerlingenapi.informatsoftware.be/1/students/";

    public function __construct($endpoint, $object, $extend = null, $apiVersion = 1)
    {
        $this->endpoint = $endpoint;
        $this->object = $object;
        $this->extend = $extend;
        $this->apiVersion = $apiVersion;
    }

    public function get($instituteNumber, $id = null)
    {
        $endpoint = Path::normalize(str_replace("<extend>", $this->extend, $this->endpoint));
        $endpoint = Path::normalize(str_replace("<id>", $id, $endpoint));

        $requestHeaders = [
            "InstituteNo" => $instituteNumber
        ];

        $requestQuery = [
            "schoolyear" => INFORMAT_CURRENT_SCHOOLYEAR,
            "structure" => Arrays::first((new Setting)->get("informat.structure"))->value
        ];

        $result = $this->execute($endpoint, $requestHeaders, $requestQuery);

        $objects = [];
        if ($result) {
            foreach ($result as $res) $objects[] = new $this->object($res);
        }

        return $objects;
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
