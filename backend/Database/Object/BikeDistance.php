<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\Mapping;
use Helpers\CString;
use Ouzo\Utilities\Strings;

class BikeDistance extends CustomObject
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
        "pricePerKm" => "double",
        "userMainSchoolId" => "int",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "userAddress" => ['startId' => \Database\Repository\UserAddress::class],
        "startSchool" => ['startId' => \Database\Repository\SchoolAddress::class],
        "endSchool" => ["endSchoolId" => \Database\Repository\School::class],
        "userMainSchool" => ["userMainSchoolId" => \Database\Repository\School::class]
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
        $this->formatted->badge->color = "<span class=\"badge p-2 bg-{$this->color} rounded-circle\" style=\"margin-top: 2px\"></span>";

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
