<?php

namespace Database\Object\Bike;

use Helpers\HTML;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation\Navigation;
use Database\Interface\CustomObject;
use Database\Repository\Bike\DistanceType;

class Distance extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "userId" => "int",
        "type" => "string",
        "startId" => "int",
        "endSchoolId" => "int",
        "distance" => "double",
        "alias" => "string",
        "color" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "userAddress" => ['startId' => \Database\Repository\User\Address::class],
        "startSchool" => ['startId' => \Database\Repository\School\Address::class],
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
