<?php

namespace Database\Object\Accident;

use stdClass;
use Helpers\HTML;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;
use Database\Repository\Accident\Party;
use Database\Repository\Accident\Status;
use Database\Repository\Accident\Location;
use Database\Repository\Navigation\Setting;
use Database\Repository\Navigation\Navigation;

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
        "informatStudentRelationId" => "int",
        "informatStudentEmailId" => "int",
        "informatStudentNumberId" => "int",
        "informatStudentBankId" => "int",
        "informatStudentAddressId" => "int",
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
        "witnessId" => "int",
        "creationDateTime" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School\School::class
        ],
        "creatorUser" => [
            "creatorUserId" => \Database\Repository\User\User::class
        ],
        "informatClass" => [
            "informatSubgroupId" => \Database\Repository\Informat\ClassGroup::class
        ],
        "informatStudent" => [
            "informatStudentId" => \Database\Repository\Informat\Student::class
        ],
        "informatStudentRelation" => [
            "informatStudentRelationId" => \Database\Repository\Informat\StudentRelation::class
        ],
        "informatStudentEmail" => [
            "informatStudentEmailId" => \Database\Repository\Informat\StudentEmail::class
        ],
        "informatStudentNumber" => [
            "informatStudentNumberId" => \Database\Repository\Informat\StudentNumber::class
        ],
        "informatStudentBank" => [
            "informatStudentBankId" => \Database\Repository\Informat\StudentBank::class
        ],
        "informatStudentAddress" => [
            "informatStudentAddressId" => \Database\Repository\Informat\StudentAddress::class
        ],
        "supervisor" => [
            "informatSupervisorId" => \Database\Repository\Informat\Employee::class
        ],
        "partyExternalCountry" => [
            "partyExternalCountryId" => \Database\Repository\General\Country::class
        ],
        "witness" => [
            "witnessId" => \Database\Repository\Informat\Employee::class
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

        $this->formatted->party = (new Party)->getById($this->party)->name;
        $this->formatted->badge->status = (new Status)->getById($this->status)->formatted->badge->name;

        $this->createNumber();
        $this->createLocation();
    }

    private function createNumber()
    {
        $format = (new Setting)->getByNavigationIdAndKey((new Navigation)->getByParentIdAndLink(0, "accident")->id, "format")->value;
        $this->formatted->number = $format;

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
        $locationRepo = new Location;

        [$main, $sub] = explode("-", $this->location);
        $mainLocation = $locationRepo->getById($main)->name;
        $subLocation = $locationRepo->getByIdAndCategoryId($sub, $main)->name;

        $this->formatted->location = "{$mainLocation} - {$subLocation}";
    }
}
