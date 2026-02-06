<?php

namespace Database\Object\Signage;

use Security\CustomObject;

class PlaylistItem extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "playlistId" => self::TYPE_INTEGER,
        "mediaId" => self::TYPE_INTEGER,
        "duration" => self::TYPE_INTEGER,
        "order" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
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
