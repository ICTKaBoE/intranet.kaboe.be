<?php

namespace O365\Interfaces;

use Database\Repository\Setting;
use GuzzleHttp\Client;
use Microsoft\Graph\Graph;

class Repository
{
    protected $selectProperties;
    protected $requestEndpoint;
    protected $requestParameters = [];
    protected $requestHeaders = [];

    public function __construct($endpoint, $returnClass)
    {
        $settings = new Setting;
        $this->endpoint = $endpoint;
        $this->returnClass = $returnClass;

        $this->tenantId = $settings->get("o365.tenant.id")[0]->value;
        $this->clientId = $settings->get("o365.client.id")[0]->value;
        $this->clientSecret = $settings->get("o365.client.secret")[0]->value;
        $this->defaultUsername = $settings->get("o365.default.username")[0]->value;
        $this->defaultPassword = $settings->get("o365.default.password")[0]->value;

        $guzzle = new Client();
        $url = "https://login.microsoftonline.com/" . $this->tenantId . "/oauth2/token?api-version=1.0";
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'password',
                'username' => $this->defaultUsername,
                'password' => $this->defaultPassword
            ],
        ])->getBody()->getContents());

        $this->accessToken = $token->access_token;
    }

    public function createRequest()
    {
        $this->requestEndpoint = $this->endpoint;
        $this->requestParameters = [];
        $this->requestHeaders = [];
        return $this;
    }

    public function setOid($oid)
    {
        $this->requestEndpoint .= "/" . $oid;
        return $this;
    }

    public function getMembers()
    {
        $this->requestEndpoint .= "/transitiveMembers";
        return $this;
    }

    public function setSelect($select = null)
    {
        if (is_null($select)) $select = $this->selectProperties;
        $this->requestParameters["\$select"] = join(',', $select);
        return $this;
    }

    public function setFilter($filter)
    {
        $this->requestParameters["\$filter"] = $filter;
        return $this;
    }

    public function setSearch($search)
    {
        $this->requestParameters["\$search"] = $search;
        return $this;
    }

    public function setCount($count = "true")
    {
        $this->requestParameters["\$count"] = $count;
        return $this;
    }

    public function addHeaders($key, $value)
    {
        $this->requestHeaders[$key] = $value;
        return $this;
    }

    public function doRequest($returnType = null, $pageSize = 999)
    {
        try {
            $request = $this->createRequestObject($returnType, $pageSize);
            return $request->execute();
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }

    public function doRequestAllPages($returnType = null, $pageSize = 999)
    {
        try {
            $request = $this->createRequestObject($returnType, $pageSize);
            $result = array_merge([], $request->getPage());

            while (!$request->isEnd()) {
                $result = array_merge($result, $request->getPage());
            }

            return $result;
        } catch (\Exception $e) {
            die(var_dump($e->getMessage()));
        }
    }

    private function createRequestObject($returnType = null, $pageSize = 999)
    {
        if (is_null($returnType)) $returnType = $this->returnClass;

        $this->setCount();
        $this->addHeaders("ConsistencyLevel", "eventual");
        if (!empty($this->requestParameters)) $this->requestEndpoint .= "?" . http_build_query($this->requestParameters);

        $graph = new Graph();
        $graph->setAccessToken($this->accessToken);
        $request = $graph->createCollectionRequest("GET", $this->requestEndpoint);
        $request->setPageSize($pageSize);

        if (!empty($this->requestHeaders)) {
            foreach ($this->requestHeaders as $key => $value) {
                $request->addHeaders([$key => $value]);
            }
        }

        $request->setReturnType($returnType);
        return $request;
    }
}
