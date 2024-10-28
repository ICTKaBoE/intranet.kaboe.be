<?php

namespace Database\Object\Order;

use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class Purchase extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "number" => "int",
        "schoolId" => "int",
        "creatorUserId" => "int",
        "acceptorUserId" => "string",
        "supplierId" => "int",
        "status" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User::class],
        "acceptorUser" => ["acceptorUserId" => \Database\Repository\User::class],
        "school" => ["schoolId" => \Database\Repository\School::class],
        "supplier" => ['supplierId' => \Database\Repository\Order\Supplier::class]
    ];

    public function init()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;

        $this->formatted->badge->status = "<span class=\"badge text-white bg-{$settings['status'][$this->status]['color']}\" style=\"margin-top: 2px\">{$settings['status'][$this->status]['name']}</span>";
        $this->formatted->acceptor = is_array($this->linked->acceptorUser) ? join('<br />', Arrays::map($this->linked->acceptorUser, fn($a) => $a->formatted->fullName)) : $this->linked->acceptorUser->formatted->fullName;

        // $this->_lockedForm = (Strings::equal(User::getLoggedInUser()->id, $this->creatorUserId) && !Strings::equal(User::getLoggedInUser()->id, $this->assignedToUserId) || Strings::equal($this->status, 'C'));

        $this->createNumber();
    }

    private function createNumber()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $this->formatted->number = $settings['format'];

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
}
