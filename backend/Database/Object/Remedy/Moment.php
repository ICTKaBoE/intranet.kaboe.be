<?php

namespace Database\Object\Remedy;

use Helpers\Date;
use stdClass;
use Ouzo\Utilities\Clock;
use Security\CustomObject;

class Moment extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolyearId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "departmentId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "typeId" => self::TYPE_INTEGER,
        "description" => self::TYPE_STRING,
        "seats" => self::TYPE_INTEGER,
        "full" => self::TYPE_BOOLEAN,
        "dayOfWeek" => self::TYPE_INTEGER,
        "date" => self::TYPE_DATE,
        "hourId" => self::TYPE_INTEGER,
        "courseId" => self::TYPE_INTEGER,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "userId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "schoolyear" => ["schoolyearId" => \Database\Repository\General\Schoolyear::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "department" => ["departmentId" => \Database\Repository\School\Department::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class],
        "type" => ["typeId" => \Database\Repository\Remedy\Type::class],
        "hour" => ["hourId" => \Database\Repository\School\Hour::class],
        "course" => ["courseId" => \Database\Repository\School\Course::class],
        "informatEmployee" => ["informatEmployeeId" => \Database\Repository\Informat\Employee::class]
    ];

    public function init()
    {
        $this->isPast = ($this->date ? Clock::at($this->date . " " . $this->linked->hour->start)->isBefore(Clock::now()) : false);

        $this->formatted->date = new stdClass;
        $this->formatted->date->display = is_null($this->date) ? ucfirst(Date::dayOfWeekToString($this->dayOfWeek)) : Clock::at($this->date)->format("d/m/Y");
        $this->formatted->date->sort = is_null($this->date) ? ucfirst(Date::dayOfWeekToString($this->dayOfWeek)) :  Clock::at($this->date)->format("U");

        $this->formatted->buildingRoom = "{$this->linked->building->name} - {$this->linked->room->formatted->name}";

        $this->formatted->shortDescription = ucfirst(Date::dayOfWeekToString(is_null($this->date) ? $this->dayOfWeek : Clock::at($this->date)->format("w"))) . " " . (is_null($this->date) ? "" : $this->formatted->date->display) . " ({$this->linked->hour->formatted->startEnd})" . ($this->linked->informatEmployee ? " - Door {$this->linked->informatEmployee->formatted->fullNameReversed}" : "") . ($this->description ? " - {$this->description}" : "") . ($this->full ? " (volzet)" : "");
        $this->formatted->shortDescriptionWithType = "{$this->linked->type->name}: {$this->formatted->shortDescription}";

        $this->formatted->seats = $this->seats == 0 ? "Onbeperkt" : (($this->linked->type->manualAssignDate) ? $this->seats : $this->seats . ($this->full ? " (vol)" : ""));
    }
}
