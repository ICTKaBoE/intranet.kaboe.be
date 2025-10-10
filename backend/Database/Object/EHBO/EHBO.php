<?php

namespace Database\Object\EHBO;

use stdClass;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;

class EHBO extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "creationDateTime" => self::TYPE_DATETIME,
        "schoolId" => self::TYPE_INTEGER,
        "place" => self::TYPE_STRING,
        "description" => self::TYPE_STRING,
        "descriptionOther" => self::TYPE_STRING,
        "firstHelpDateTime" => self::TYPE_DATETIME,
        "firstHelp" => self::TYPE_STRING,
        "firstHelpOther" => self::TYPE_STRING,
        "victimType" => self::TYPE_STRING,
        "victimId" => self::TYPE_INTEGER,
        "witness" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "description" => ["description" => \Database\Repository\EHBO\Description::class],
        "firstHelp" => ["firstHelp" => \Database\Repository\EHBO\FirstHelp::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "victimEmployee" => ["victimId" => \Database\Repository\Informat\Employee::class],
        "victimStudent" => ["victimId" => \Database\Repository\Informat\Student::class],
    ];

    public function init()
    {
        $this->linked->victim = Strings::equal($this->victimType, "S") ? $this->linked->victimStudent : $this->linked->victimEmployee;
        $this->formatted->description = Strings::equal($this->description, "O") ? $this->descriptionOther : $this->linked->description->name;
        $this->formatted->firstHelp = Strings::equal($this->firstHelp, "O") ? $this->firstHelpOther : $this->linked->firstHelp->name;
        $this->formatted->firstHelpWithDateTime = $this->formatted->firstHelp . " - " . Clock::at($this->firstHelpDateTime)->format("d/m/Y H:i");

        $this->formatted->creationDateTime = new stdClass;
        $this->formatted->creationDateTime->display = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
        $this->formatted->creationDateTime->sort = Clock::at($this->creationDateTime)->format("U");
    }
}
