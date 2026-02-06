<?php

namespace Database\Object\Signage;

use Security\CustomObject;
use Security\FileSystem;

class Media extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "alias" => self::TYPE_STRING,
        "link" => self::TYPE_STRING,
        "size" => self::TYPE_DOUBLE,
        "length" => self::TYPE_STRING,
        "duration" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School\School::class
        ]
    ];

    public function init()
    {
        $this->mediaImage = $this->type == "I" ? $this->link : null;
        $this->mediaVideo = $this->type == "V" ? $this->link : null;
        $this->mediaLink = $this->type == "L" ? $this->link : null;

        $this->formatted->link = $this->type == "L" ? $this->link : FileSystem::GetDownloadLink(LOCATION_UPLOAD . "/signage/{$this->link}");
    }
}
