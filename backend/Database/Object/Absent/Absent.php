<?php

namespace Database\Object\Absent;

use stdClass;
use Ouzo\Utilities\Clock;
use Database\Interface\CustomObject;
use Database\Repository\Absent\Note;
use Database\Repository\Absent\Payment;
use Database\Repository\Absent\Substitute;
use Ouzo\Utilities\Strings;

class Absent extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "creationDateTime" => self::TYPE_DATETIME,
        "schoolId" => self::TYPE_INTEGER,
        "absentUserId" => self::TYPE_INTEGER,
        "substituteBy" => self::TYPE_STRING,
        "substituteByOther" => self::TYPE_STRING,
        "volume" => self::TYPE_STRING,
        "start" => self::TYPE_DATE,
        "end" => self::TYPE_DATE,
        "paymentOfSubstitute" => self::TYPE_STRING,
        "paymentOfSubstituteOther" => self::TYPE_STRING,
        "absentNoteReceived" => self::TYPE_STRING,
        "notes" => self::TYPE_STRING,
        "finished" => self::TYPE_BOOLEAN,
        "finishedByUserId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "absentUser" => ["absentUserId" => \Database\Repository\User\User::class],
        "finishedByUser" => ["finishedByUserId" => \Database\Repository\User\User::class]
    ];

    public function init()
    {
        $this->formatted->substituteBy = Strings::equal($this->substituteBy, "O") ? $this->substituteByOther : ((new Substitute)->getById($this->substituteBy)->name ?: null);
        $this->formatted->paymentOfSubstitute = Strings::equal($this->paymentOfSubstitute, "O") ? $this->paymentOfSubstituteOther : ((new Payment)->getById($this->paymentOfSubstitute)->name ?: null);
        $this->formatted->absentNoteReceived = (new Note)->getById($this->absentNoteReceived)->name ?: null;

        $this->formatted->creationDateTime = new stdClass;
        $this->formatted->creationDateTime->display = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
        $this->formatted->creationDateTime->sort = Clock::at($this->creationDateTime)->format("U");

        $this->formatted->start = new stdClass;
        $this->formatted->start->display = Clock::at($this->start)->format("d/m/Y");
        $this->formatted->start->sort = Clock::at($this->start)->format("U");

        $this->formatted->end = new stdClass;
        $this->formatted->end->display = Strings::equal($this->end, "-0001-11-30") ? null : Clock::at($this->end)->format("d/m/Y");
        $this->formatted->end->sort = Strings::equal($this->end, "-0001-11-30") ? null : Clock::at($this->end)->format("U");

        $this->_lockedForm = $this->finished;
    }
}
