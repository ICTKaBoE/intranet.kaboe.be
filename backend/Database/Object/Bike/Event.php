<?php

namespace Database\Object\Bike;

use Database\Interface\CustomObject;
use Database\Repository\Mapping;
use Helpers\CString;
use Ouzo\Utilities\Strings;

class Event extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "date" => "date",
        "bikeDistanceId" => "int",
        "type" => "string",
        "userId" => "int",
        "startId" => "int",
        "endSchoolId" => "int",
        "distance" => "double",
        "alias" => "string",
        "color" => "string",
        "pricePerKm" => "double",
        "userMainSchoolId" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "user" => ['userId' => \Database\Repository\User\User::class],
        "userAddress" => ['startId' => \Database\Repository\User\Address::class],
        "startSchool" => ['startId' => \Database\Repository\School\Address::class],
        "endSchool" => ["endSchoolId" => \Database\Repository\School\School::class],
        "userMainSchool" => ["userMainSchoolId" => \Database\Repository\School\School::class]
    ];

    private $textColors = [
        "black" => ['azure', 'orange', 'yellow', 'lime', 'cyan'],
        "white" => ['blue', 'indigo', 'purple', 'red', 'green', 'teal', 'pink']
    ];

    public function init()
    {
        $this->startAddress = (Strings::equal($this->type, "HW") ? $this->linked->userAddress->formatted->address : $this->linked->startSchool->formatted->addressWithSchool);
        $this->mapped->type = (new Mapping)->get("bike/distance/type/{$this->type}")[0]->value;
        $this->formatted->distance = CString::formatNumber($this->distance, 2) . "km";
        $this->formatted->distanceWithDouble = $this->formatted->distance . " (" . CString::formatNumber($this->distance * 2, 2) . "km)";

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
