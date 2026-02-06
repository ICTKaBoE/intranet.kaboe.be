<?php

namespace Database\Object\Export;

use stdClass;
use Ouzo\Utilities\Clock;
use Security\CustomObject;
use Ouzo\Utilities\Strings;

class Export extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "navigationId" => self::TYPE_INTEGER,
        "controller" => self::TYPE_STRING,
        "function" => self::TYPE_STRING,
        "parameters" => self::TYPE_JSON,
        "status" => self::TYPE_STRING,
        "filename" => self::TYPE_STRING,
        "start" => self::TYPE_DATETIME,
        "end" => self::TYPE_DATETIME
    ];

    protected $linkedAttributes = [
        "navigation" => ["navigationId" => \Database\Repository\Navigation\Navigation::class],
        "status" => ["status" => \Database\Repository\Export\Status::class]
    ];

    public function init()
    {
        $this->formatted->start = new stdClass;
        $this->formatted->start->display = (is_null($this->start) || Strings::equal($this->start, "-0001-11-30")) ? null : Clock::at($this->start)->format("d/m/Y H:i:s");
        $this->formatted->start->sort = (is_null($this->start) || Strings::equal($this->start, "-0001-11-30")) ? null : Clock::at($this->start)->format("U");

        $this->formatted->end = new stdClass;
        $this->formatted->end->display = (is_null($this->end) || Strings::equal($this->end, "-0001-11-30")) ? null : Clock::at($this->end)->format("d/m/Y H:i:s");
        $this->formatted->end->sort = (is_null($this->end) || Strings::equal($this->end, "-0001-11-30")) ? null : Clock::at($this->end)->format("U");
    }
}
