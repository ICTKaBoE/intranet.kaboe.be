<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\HTML;

class SecurityGroup extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "name" => "string",
        "permission" => "binary",
        "m365GroupId" => "guid",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $this->read = $this->permission[0];
        $this->create = $this->permission[1];
        $this->update = $this->permission[2];
        $this->delete = $this->permission[3];
        $this->export = $this->permission[4];
        $this->changeSettings = $this->permission[5];
        $this->admin = $this->permission[6];

        $this->formatted->icon->read = HTML::Icon(($this->permission[0] ? "eye" : "x"), color: ($this->permission[0] ? "green" : "red"));
        $this->formatted->icon->create = HTML::Icon(($this->permission[1] ? "eye" : "x"), color: ($this->permission[1] ? "green" : "red"));
        $this->formatted->icon->update = HTML::Icon(($this->permission[2] ? "eye" : "x"), color: ($this->permission[2] ? "green" : "red"));
        $this->formatted->icon->delete = HTML::Icon(($this->permission[3] ? "eye" : "x"), color: ($this->permission[3] ? "green" : "red"));
        $this->formatted->icon->export = HTML::Icon(($this->permission[4] ? "eye" : "x"), color: ($this->permission[4] ? "green" : "red"));
        $this->formatted->icon->changeSettings = HTML::Icon(($this->permission[5] ? "eye" : "x"), color: ($this->permission[5] ? "green" : "red"));
        $this->formatted->icon->admin = HTML::Icon(($this->permission[6] ? "eye" : "x"), color: ($this->permission[6] ? "green" : "red"));
    }
}
