<?php

namespace Database\Object\HR;

use Database\Repository\Navigation\Navigation;
use Database\Repository\Navigation\Setting;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\CustomObject;
use Security\Input;
use stdClass;

class HR extends CustomObject
{
    const PARAMETERS = [
        "insz" => "Rijksregisternummer",
        "name" => "Naam",
        "firstName" => "Voornaam",
        "sex" => "Geslacht",
        "phone" => "GSM-nummer (persoonlijk)",
        "email" => "E-mail (persoonlijk)",
        "statusId" => "Status",
        "informatId" => "Intern nummer",
        "schoolEmail" => "E-mail (school)",
        "cv" => "CV",
        "certificate" => "Diploma",
        "proof" => "Bekwaamheidsbewijs",
        "start" => "Startdatum",
        "end" => "Einddatum",
        "laptopUsage" => "Laptopgebruik",
        "functionDescriptionReceived" => "Functieomschrijving ontvangen",
        "roleId" => "Functie(s)",
        "info" => "Informatie",
    ];

    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "insz" => self::TYPE_STRING,
        "name" => self::TYPE_STRING,
        "firstName" => self::TYPE_STRING,
        "sex" => self::TYPE_STRING,
        "phone" => self::TYPE_STRING,
        "email" => self::TYPE_STRING,
        "statusId" => self::TYPE_INTEGER,
        "informatId" => self::TYPE_INTEGER,
        "schoolEmail" => self::TYPE_STRING,
        "cv" => self::TYPE_STRING,
        "certificate" => self::TYPE_STRING,
        "proof" => self::TYPE_BOOLEAN,
        "start" => self::TYPE_DATE,
        "end" => self::TYPE_DATE,
        "laptopUsage" => self::TYPE_INTEGER,
        "functionDescriptionReceived" => self::TYPE_BOOLEAN,
        "roleId" => self::TYPE_STRING,
        "info" => self::TYPE_STRING,
        "lastActionUserId" => self::TYPE_INTEGER,
        "deleted" => self::TYPE_BOOLEAN,
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "status" => ["statusId" => \Database\Repository\HR\Status::class],
        "role" => ["roleId" => \Database\Repository\HR\Role::class],
        "lastActionUser" => ["lastActionUserId" => \Database\Repository\User\User::class],
    ];

    public function init()
    {
        if (Strings::equal($this->end, "-0001-11-30")) $this->end = null;
        $sex = Arrays::toMap(explode(",", (new Setting)->getByNavigationIdAndKey((new Navigation)->getByLinkAndType("hr", "M")->id, "sex")->value), fn($i) => explode(":", $i)[0], fn($i) => explode(":", $i)[1]);
        $laptopUsage = Arrays::toMap(explode(",", (new Setting)->getByNavigationIdAndKey((new Navigation)->getByLinkAndType("hr", "M")->id, "laptopUsage")->value), fn($i) => explode(":", $i)[0], fn($i) => explode(":", $i)[1]);

        $this->formatted->fullName = Input::createDisplayName("{{FN}} {{LN}}", $this->firstName, $this->name);
        $this->formatted->fullNameReversed = Input::createDisplayName("{{LN}} {{FN}}", $this->firstName, $this->name);

        $this->formatted->start = new stdClass;
        $this->formatted->start->display = Clock::at($this->start)->format("d/m/Y");
        $this->formatted->start->sort = Clock::at($this->start)->format("U");

        $this->formatted->end = new stdClass;
        $this->formatted->end->display = is_null($this->end) ? null : Clock::at($this->end)->format("d/m/Y");
        $this->formatted->end->sort = is_null($this->end) ? null : Clock::at($this->end)->format("U");

        $this->formatted->function = is_array($this->linked->role) ? implode("<br />", Arrays::map($this->linked->role, fn($r) => $r->formatted->nameWithSchool ?: $r->name)) : ($this->linked->role->formatted->nameWithSchool ?: $this->linked->role->name);
        $this->formatted->functionDescriptionReceived = $this->functionDescriptionReceived ? "Ja" : "Nee";

        $this->formatted->roleId = $this->formatted->function;
        $this->formatted->statusId = $this->linked->status->name;
        $this->formatted->laptopUsage = Arrays::getValue($laptopUsage, $this->laptopUsage);
        $this->formatted->sex = Arrays::getValue($sex, $this->sex);
        $this->formatted->listOfDetails = "";

        $this->formatted->url = Helpers::request()->getReferer() . "hr/overview/" . $this->guid;

        foreach (self::PARAMETERS as $key => $value) {
            $this->formatted->listOfDetails .= "<b>" . self::PARAMETERS[$key] . "</b>: " . (($this->formatted->$key instanceof stdClass ? $this->formatted->$key->display : $this->formatted->$key) ?: $this->$key) . "<br />";
        }
    }
}
