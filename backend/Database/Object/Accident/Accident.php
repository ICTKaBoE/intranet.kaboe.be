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
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "status" => self::TYPE_STRING,
        "schoolId" => self::TYPE_INTEGER,
        "creatorUserId" => self::TYPE_INTEGER,
        "informatSubgroupId" => self::TYPE_INTEGER,
        "informatStudentId" => self::TYPE_INTEGER,
        "informatStudentRelationId" => self::TYPE_INTEGER,
        "informatStudentEmailId" => self::TYPE_INTEGER,
        "informatStudentNumberId" => self::TYPE_INTEGER,
        "informatStudentBankId" => self::TYPE_INTEGER,
        "informatStudentAddressId" => self::TYPE_INTEGER,
        "datetime" => self::TYPE_DATETIME,
        "description" => self::TYPE_STRING,
        "location" => self::TYPE_STRING,
        "exactLocation" => self::TYPE_STRING,
        "transport" => self::TYPE_STRING,
        "supervision" => self::TYPE_BOOLEAN,
        "informatSupervisorId" => self::TYPE_INTEGER,
        "party" => self::TYPE_STRING,
        "partyExternalName" => self::TYPE_STRING,
        "partyExternalFirstName" => self::TYPE_STRING,
        "partyExternalSex" => self::TYPE_STRING,
        "partyExternalStreet" => self::TYPE_STRING,
        "partyExternalNumber" => self::TYPE_INTEGER,
        "partyExternalBus" => self::TYPE_STRING,
        "partyExternalZipcode" => self::TYPE_STRING,
        "partyExternalCity" => self::TYPE_STRING,
        "partyExternalCountryId" => self::TYPE_INTEGER,
        "partyExternalCompany" => self::TYPE_STRING,
        "partyExternalPolicyNumber" => self::TYPE_STRING,
        "partyOtherFullName" => self::TYPE_STRING,
        "partyOtherFullAddress" => self::TYPE_STRING,
        "partyOtherBirthDay" => self::TYPE_DATE,
        "partyInstallReason" => self::TYPE_STRING,
        "police" => self::TYPE_BOOLEAN,
        "policeName" => self::TYPE_STRING,
        "policePVNumber" => self::TYPE_STRING,
        "witnessId" => self::TYPE_INTEGER,
        "creationDateTime" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
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
            $this->formatted->number = str_replace($hashes, str_pad($this->id, $count, 0, STR_PAD_LEFT), $this->formatted->number);
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
