<?php

namespace Database\Object\Management;

use Helpers\CString;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation\Navigation;
use Security\CustomObject;
use Database\Repository\Navigation\Setting;

class Printer extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "mode" => self::TYPE_STRING,
        "manufacturer" => self::TYPE_STRING,
        "model" => self::TYPE_STRING,
        "serialnumber" => self::TYPE_STRING,
        "ip" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class]
    ];

    public function init()
    {
        $this->formatted->mode = $this->mode;
        $this->formatted->manModel = "{$this->manufacturer} {$this->model}";
        $this->formatted->ip = CString::formatLink($this->ip);
    }
}
