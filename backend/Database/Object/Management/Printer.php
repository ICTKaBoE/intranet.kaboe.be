<?php

namespace Database\Object\Management;

use Helpers\CString;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class Printer extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "buildingId" => "int",
        "roomId" => "int",
        "name" => "string",
        "mode" => "string",
        "manufacturer" => "string",
        "model" => "string",
        "serialnumber" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];

    public function init()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $this->formatted->mode = $settings['printer']['mode'][$this->mode]['name'];
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
    }
}
