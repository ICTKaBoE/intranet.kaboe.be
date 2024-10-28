<?php

namespace M365\Interface;

use M365\GraphHelper;
use Microsoft\Graph\Core\Tasks\PageIterator;
use stdClass;

class Repository extends stdClass
{
    private $token = null;
    private $objects = [];

    public function __construct()
    {
        if (!$this->token) {
            GraphHelper::initializeGraphForAppOnlyAuth();
            $this->token = GraphHelper::getAppOnlyToken();
        }
    }

    public function iterate($response)
    {
        $this->objects = [];

        $iterator = new PageIterator($response, GraphHelper::$appClient->getRequestAdapter());
        $iterator->iterate(function ($obj) {
            $this->objects[] = $obj;
            return true;
        });

        return $this->objects;
    }
}
