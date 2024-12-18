<?php

namespace Database\Object;

use stdClass;
use Helpers\HTML;
use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class Accident extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "number" => "int",
        "status" => "string",
        "schoolId" => "int",
        "creatorUserId" => "int",
        "informatSubgroupId" => "int",
        "informatStudentId" => "int",
        "datetime" => "datetime",
        "description" => "string",
        "location" => "string",
        "exactLocation" => "string",
        "transport" => "string",
        "supervision" => "bool",
        "informatSupervisorId" => "int",
        "party" => "string",
        "partyExternalName" => "string",
        "partyExternalFirstName" => "string",
        "partyExternalSex" => "string",
        "partyExternalStreet" => "string",
        "partyExternalNumber" => "int",
        "partyExternalBus" => "string",
        "partyExternalZipcode" => "string",
        "partyExternalCity" => "string",
        "partyExternalCountryId" => "int",
        "partyExternalCompany" => "string",
        "partyExternalPolicyNumber" => "string",
        "partyOtherFullName" => "string",
        "partyOtherFullAddress" => "string",
        "partyOtherBirthDay" => "date",
        "partyInstallReason" => "string",
        "police" => "boolean",
        "policeName" => "string",
        "policePVNumber" => "string",
        "creationDateTime" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School::class
        ],
        "creatorUser" => [
            "creatorUserId" => \Database\Repository\User::class
        ],
        "informatClass" => [
            "informatSubgroupId" => \Database\Repository\Informat\ClassGroup::class
        ],
        "informatStudent" => [
            "informatStudentId" => \Database\Repository\Informat\Student::class
        ],
        "supervisor" => [
            "informatSupervisorId" => \Database\Repository\Informat\Employee::class
        ],
        "partyExternalCountry" => [
            "partyExternalCountryId" => \Database\Repository\Country::class
        ]
    ];

    public function init()
    {
        $this->formatted->creationDateTime = new stdClass;
        $this->formatted->creationDateTime->display = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
        $this->formatted->creationDateTime->sort = Clock::at($this->creationDateTime)->format("U");

        $this->formatted->date = Clock::at($this->datetime)->format("d/m/Y");
        $this->formatted->day = Clock::at($this->datetime)->format("l");
        $this->formatted->time = Clock::at($this->datetime)->format("H:i");

        $this->createNumber();
        $this->createLocation();
        $this->createParty();
    }

    private function createNumber()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $this->formatted->number = $settings['format'];
        $this->formatted->badge->status = HTML::Badge($settings['status'][$this->status]['name'], backgroundColor: $settings['status'][$this->status]['color'], style: ["margin-top" => "2px"]);

        if (Strings::contains($this->formatted->number, "#")) {
            $count = substr_count($this->formatted->number, "#");
            $hashes = "";
            for ($i = 0; $i < $count; $i++) $hashes .= "#";
            $this->formatted->number = str_replace($hashes, str_pad($this->number, $count, 0, STR_PAD_LEFT), $this->formatted->number);
        }

        if (Strings::contains($this->formatted->number, "Y")) {
            $count = substr_count($this->formatted->number, "Y");
            $hashes = "";
            for ($i = 0; $i < $count; $i++) $hashes .= "Y";
            $this->formatted->number = str_replace($hashes, Clock::at($this->creationDateTime)->format($hashes), $this->formatted->number);
        }
    }

    private function createLocation()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $locations = $settings["location"];

        [$main, $sub] = explode("-", $this->location);
        $mainLocation = $locations[$main]['name'];
        $subLocation = $locations[$main]['sub'][$sub];

        $this->formatted->location = "{$mainLocation} - {$subLocation}";
    }

    private function createParty()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $parties = $settings["parties"];

        $this->formatted->party = $parties[$this->party]['name'];
    }
}
