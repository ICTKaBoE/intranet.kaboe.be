<?php

namespace Database\Object\Helpdesk;

use stdClass;
use Security\User;
use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;
use Helpers\HTML;

class Ticket extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "guid" => "string",
        "number" => "int",
        "creatorUserId" => "int",
        "assignedToUserId" => "int",
        "status" => "string",
        "priority" => "string",
        "schoolId" => "int",
        "roomId" => "int",
        "category" => "string",
        "assetId" => "int",
        "creationDateTime" => "datetime",
        "lastActionDateTime" => "datetime",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "assignedToUser" => ["assignedToUserId" => \Database\Repository\User\User::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "computer" => ['assetId' => \Database\Repository\Management\Computer::class],
        "ipad" => ['assetId' => \Database\Repository\Management\IPad::class],
        "beamer" => ['assetId' => \Database\Repository\Management\Beamer::class],
        "printer" => ['assetId' => \Database\Repository\Management\Printer::class],
        "firewall" => ['assetId' => \Database\Repository\Management\Firewall::class],
        "switch" => ['assetId' => \Database\Repository\Management\MSwitch::class],
        "accesspoint" => ['assetId' => \Database\Repository\Management\AccessPoint::class]
    ];

    public function init()
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;

        $this->formatted->badge->status = HTML::Badge($settings['status'][$this->status]['name'], backgroundColor: $settings['status'][$this->status]['color'], style: ["margin-top" => "2px"]);
        $this->formatted->badge->priority = HTML::Badge($settings['priority'][$this->priority]['name'], backgroundColor: $settings['priority'][$this->priority]['color'], style: ["margin-top" => "2px"]);

        $category = explode("-", $this->category);
        $this->formatted->subject = $settings['category'][$category[0]]['name'];
        if (!is_null($category[1])) $this->formatted->subject .= " - " . $settings['category'][$category[0]]['sub'][$category[1]];

        if (Strings::equal($category[0], "L") || Strings::equal($category[0], "D")) $this->formatted->subject = $this->linked->computer->name . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "I")) $this->formatted->subject = $this->linked->ipad->name . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "B")) $this->formatted->subject = $this->linked->beamer->serialnumber . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "P")) $this->formatted->subject = $this->linked->printer->name . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "F")) $this->formatted->subject = $this->linked->firewall->hostname . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "S")) $this->formatted->subject = $this->linked->switch->name . " - " . $this->formatted->subject;
        else if (Strings::equal($category[0], "A")) $this->formatted->subject = $this->linked->accesspoint->name . " - " . $this->formatted->subject;

        $this->formatted->link = "https://intranet.kaboe.be/helpdesk/mine/{$this->guid}";
        $this->formatted->assignedLink = "https://intranet.kaboe.be/helpdesk/assigned/{$this->guid}";

        $this->_lockedForm = (Strings::equal(User::getLoggedInUser()->id, $this->creatorUserId) && !Strings::equal(User::getLoggedInUser()->id, $this->assignedToUserId) || Strings::equal($this->status, 'C'));

        $this->createNumber();
        $this->createAge();
        $this->createLastActivity();
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

    private function createAge()
    {
        $age = Clock::at($this->creationDateTime)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0 && $age->i == 0) $this->formatted->age = $age->s . " seconden";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0 && $age->h == 0) $this->formatted->age = $age->i . " minuten";
        else if ($age->y == 0 && $age->m == 0 && $age->d == 0) $this->formatted->age = $age->h . " uren";
        else if ($age->y == 0 && $age->m == 0) $this->formatted->age = $age->d . " dagen";
        else if ($age->y == 0) $this->formatted->age = $age->m . " maanden";
        else $this->formatted->age = $age->y . " jaren";
    }

    private function createLastActivity()
    {
        $laage = Clock::at($this->lastActionDateTime)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0 && $laage->h == 0 && $laage->i == 0) $this->laage = $laage->s . " seconden";
        else if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0 && $laage->h == 0) $this->laage = $laage->i . " minuten";
        else if ($laage->y == 0 && $laage->m == 0 && $laage->d == 0) $this->laage = $laage->h . " uren";
        else if ($laage->y == 0 && $laage->m == 0) $this->laage = $laage->d . " dagen";
        else if ($laage->y == 0) $this->laage = $laage->m . " maanden";

        $this->formatted->lastActivity = new stdClass;
        $this->formatted->lastActivity->display = Clock::at($this->lastActionDateTime)->format("d/m/Y H:i:s") . " ({$this->laage} geleden)";
        $this->formatted->lastActivity->sort = Clock::at($this->lastActionDateTime)->format("U");
    }
}
