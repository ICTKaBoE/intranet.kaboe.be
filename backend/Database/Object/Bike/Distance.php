<?php

namespace Database\Object\Bike;

use Helpers\HTML;
use Helpers\CString;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use Database\Repository\Bike\DistanceType;

class Distance extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "userId" => self::TYPE_INTEGER,
        "type" => self::TYPE_STRING,
        "startId" => self::TYPE_INTEGER,
        "endSchoolId" => self::TYPE_INTEGER,
        "distance" => self::TYPE_DOUBLE,
        "alias" => self::TYPE_STRING,
        "color" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "userAddress" => ['startId' => \Database\Repository\User\Address::class],
        "startSchool" => ['startId' => \Database\Repository\School\School::class],
        "endSchool" => ["endSchoolId" => \Database\Repository\School\School::class],
        "userMainSchool" => ["userMainSchoolId" => \Database\Repository\School\School::class],
        // "typeName" => ["type" => \Database\Repository\Bike\DistanceType::class]
    ];

    private $textColors = [
        "black" => ['azure', 'orange', 'yellow', 'lime', 'cyan'],
        "white" => ['blue', 'indigo', 'purple', 'red', 'green', 'teal', 'pink']
    ];

    public function init()
    {
        $this->startAddress = (Strings::equal($this->type, "HW") ? $this->linked->userAddress->formatted->address : $this->linked->startSchool->formatted->addressWithSchool);
        $this->mapped->type = (new DistanceType)->getById($this->type)->name;
        $this->formatted->distance = CString::formatNumber($this->distance, 2) . "km";
        $this->formatted->distanceWithDouble = $this->formatted->distance . " (" . CString::formatNumber($this->distance * 2, 2) . "km)";
        $this->formatted->badge->color = HTML::Badge("", null, $this->color, ["rounded-circle", "p-2"], ["margin-top" => "2px"]);

        $this->borderColor = $this->color;
        $this->textColor = "black";

        foreach ($this->textColors as $tc => $bcs) {
            foreach ($bcs as $bc) {
                if (Strings::equal($this->color, $bc)) {
                    $this->textColor = $tc;
                    break;
                }
            }
        }
    }
}
