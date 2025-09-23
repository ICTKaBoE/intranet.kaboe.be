<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\HTML;

class Document extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "type" => "string",
        "order" => "int",
        "schoolyearId" => "int",
        "schoolId" => "int",
        "studyyearId" => "int",
        "fieldId" => "int",
        "alias" => "string",
        "name" => "string",
        "ext" => "string",
        "copies" => "int",
        "dependOn" => "string",
        "deleted" => "boolean"
    ];

    public function init()
    {
        $settings = (new Navigation)->getByParentIdAndLink(0, "registration")->settings;

        $this->mapped->type = $settings["documents"]["type"][$this->type];
        $this->mapped->dependOn = $settings["documents"]["dependOn"][$this->dependOn];
        $this->formatted->icon->ext = HTML::Icon("file-type-{$this->ext}");
    }
}
