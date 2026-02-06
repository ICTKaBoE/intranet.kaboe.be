<?php

namespace Database\Object\IWE;

use stdClass;
use Helpers\HTML;
use Ouzo\Utilities\Clock;
use Security\CustomObject;
use Security\FileSystem;

class IWE extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creationDateTime" => self::TYPE_DATETIME,
        "creatorUserId" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "buildingId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "brand" => self::TYPE_STRING,
        "model" => self::TYPE_STRING,
        "amount" => self::TYPE_INTEGER,
        "ownedBySchool" => self::TYPE_BOOLEAN,
        "schoolTakesOwnership" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "building" => ["buildingId" => \Database\Repository\Management\Building::class],
        "room" => ["roomId" => \Database\Repository\Management\Room::class],
    ];

    public function init()
    {
        $this->formatted->creationDateTime = new stdClass;
        $this->formatted->creationDateTime->display = Clock::at($this->creationDateTime)->format("d/m/Y H:i:s");
        $this->formatted->creationDateTime->sort = Clock::at($this->creationDateTime)->format("U");

        $this->formatted->location = "{$this->linked->building->name} - {$this->linked->room->formatted->name}";
        $this->formatted->brandModel = "{$this->brand} ({$this->model})";

        $this->formatted->ownedBySchool = $this->ownedBySchool ? "Ja" : "Nee";
        $this->formatted->schoolTakesOwnership = $this->schoolTakesOwnership ? "Ja" : "Nee";

        $this->formatted->icon->ownedBySchool = HTML::Icon($this->ownedBySchool ? 'check' : 'x', color: $this->ownedBySchool ? 'green' : 'red');
        $this->formatted->icon->schoolTakesOwnership = HTML::Icon($this->schoolTakesOwnership ? 'check' : 'x', color: $this->schoolTakesOwnership ? 'green' : 'red');

        if (FileSystem::PathExists(LOCATION_UPLOAD . "/iwe/{$this->guid}/manual.*")) {
            $file = FileSystem::getFiles(LOCATION_UPLOAD . "/iwe/{$this->guid}/manual.*")[0];
            $this->formatted->manual .= HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink($file), "Handleiding", HTML::LINK_TARGET_BLANK);
            $this->formatted->manualLink = ["text" => "Handleiding", "link" => FileSystem::GetDownloadLink($file)];
        }

        if (FileSystem::PathExists(LOCATION_UPLOAD . "/iwe/{$this->guid}/ce.*")) {
            $file = FileSystem::getFiles(LOCATION_UPLOAD . "/iwe/{$this->guid}/ce.*")[0];
            $this->formatted->ce = HTML::Link(HTML::LINK_TYPE_URL, FileSystem::GetDownloadLink($file), "CE-Kenteken", HTML::LINK_TARGET_BLANK);
            $this->formatted->ceLink = ["text" => "CE-Kenteken", "link" => FileSystem::GetDownloadLink($file)];
        }
    }
}
