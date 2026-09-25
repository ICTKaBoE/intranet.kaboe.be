<?php

namespace Database\Object\ExamSchedule;

use Helpers\CString;
use Security\CustomObject;

class EmployeeHour extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "informatEmployeeId" => self::TYPE_INTEGER,
        "schoolyearId" => self::TYPE_INTEGER,
        "workedHours" => self::TYPE_INTEGER,
        "examHours" => self::TYPE_INTEGER,
        "supervisionHours" => self::TYPE_INTEGER,
    ];

    protected $linkedAttributes = [
        "schoolyear" => ["schoolyearId" => \Database\Repository\General\Schoolyear::class]
    ];

    public function init()
    {
        $this->noExamHours = $this->workedHours - $this->examHours;
        $this->supervisionHoursToDo = ($this->examHours * 0.7) + $this->noExamHours;
        $this->balans = $this->supervisionHoursToDo - $this->supervisionHours;

        $this->formatted->supervisionHoursToDo = CString::formatNumber($this->supervisionHoursToDo, 2);
        $this->formatted->balans = CString::formatNumber($this->balans, 2);
    }
}
