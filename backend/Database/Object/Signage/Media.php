<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;
use Security\FileSystem;

class Media extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "schoolId" => "int",
        "type" => "string",
        "alias" => "string",
        "link" => "string",
        "size" => "double",
        "length" => "string",
        "duration" => "int",
        "deleted" => "boolean"
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
