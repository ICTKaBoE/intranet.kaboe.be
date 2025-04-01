<?php

namespace Database\Object\Signage;

use Database\Interface\CustomObject;

class PlaylistItem extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "playlistId" => "int",
        "mediaId" => "int",
        "duration" => "int",
        "order" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "media" => [
            "mediaId" => \Database\Repository\Signage\Media::class
        ]
    ];

    public function init()
    {
        $this->formatted->duration = gmdate("H:i:s", $this->duration);
    }
}
