<?php

namespace Database\Object\Registration;

use Database\Interface\CustomObject;
use Database\Repository\Navigation\Navigation;
use Helpers\HTML;

class Document extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "type" => self::TYPE_STRING,
        "order" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "studyyearId" => self::TYPE_INTEGER,
        "fieldId" => self::TYPE_INTEGER,
        "alias" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "ext" => self::TYPE_STRING,
        "copies" => self::TYPE_INTEGER,
        "dependOn" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    public function init()
    {
        $settings = (new Navigation)->getByParentIdAndLink(0, "registration")->settings;

        $this->mapped->type = $settings["documents"]["type"][$this->type];
        $this->mapped->dependOn = $settings["documents"]["dependOn"][$this->dependOn];
        $this->formatted->icon->ext = HTML::Icon("file-type-{$this->ext}");
    }
}
