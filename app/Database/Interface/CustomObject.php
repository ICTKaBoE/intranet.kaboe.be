<?php

namespace Database\Interface;

use Ouzo\Utilities\Arrays;

class CustomObject
{
    protected $objectAttributes = [];
    protected $linkObjects = true;

    public function __construct($attributes = [])
    {
        foreach ($this->objectAttributes as $objAtt) {
            $this->$objAtt = Arrays::getValue($attributes, $objAtt, null);
        }

        if ($this->linkObjects) $this->link();
        $this->init();
    }

    public function init()
    {
    }

    public function link()
    {
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->objectAttributes as $objAtt) $array[$objAtt] = $this->$objAtt;

        return $array;
    }
}
