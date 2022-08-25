<?php

namespace O365\Interfaces;

use GuzzleHttp\Client;
use Microsoft\Graph\Graph;

class CustomObject
{
    const TENANT_ID = "360f4fe0-2089-4e49-9c9d-df601c5edef9";
    const CLIENT_ID = "904777c1-08a6-4866-ac72-30ee26dd7724";
    const CLIENT_SECRET = "iyz7Q~CbkrygOrt8aLqAgThpJhh.qSAd2xL_0";
    const DEFAULT_USERNAME = "admin.kaboe@coltd.be";
    const DEFAULT_USERPASS = "PianomanPA";

    protected $selectProperties;
    protected $requestEndpoint;
    protected $requestParameters = [];
    protected $requestHeaders = [];

    public function __construct($endpoint, $returnClass)
    {
        $this->endpoint = $endpoint;
        $this->returnClass = $returnClass;

        $guzzle = new Client();
        $url = "https://login.microsoftonline.com/" . self::TENANT_ID . "/oauth2/token?api-version=1.0";
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'password',
                'username' => self::DEFAULT_USERNAME,
                'password' => self::DEFAULT_USERPASS
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
