<?php

namespace Database\Object\COLTDAlert;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use stdClass;

class COLTDAlert extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "from" => self::TYPE_INTEGER,
        "groupId" => self::TYPE_STRING,
        "content" => self::TYPE_STRING,
        "datetime" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "group" => ["groupId" => \Database\Repository\COLTDAlert\Group::class]
    ];

    public function init()
    {
        $this->formatted->datetime = new stdClass;
        $this->formatted->datetime->display = Clock::at($this->datetime)->format("d/m/Y H:i:s");
        $this->formatted->datetime->sort = Clock::at($this->datetime)->format("U");

        if (!is_array($this->linked->group)) $this->linked->group = [$this->linked->group];

        $this->formatted->group = $this->linked->group ? implode("<br />", Arrays::map($this->linked->group, fn($i) => $i->name)) : "";
    }
}
