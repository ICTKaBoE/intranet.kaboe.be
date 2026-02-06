<?php

namespace Database\Object\Helpdesk;

use stdClass;
use Security\User;
use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation\Navigation;
use Security\CustomObject;
use Database\Repository\Helpdesk\Category;
use Database\Repository\Helpdesk\Priority;
use Database\Repository\Helpdesk\Status;
use Database\Repository\Navigation\Setting;
use Helpers\HTML;

class Helpdesk extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "creatorUserId" => self::TYPE_INTEGER,
        "assignedToUserId" => self::TYPE_INTEGER,
        "status" => self::TYPE_INTEGER,
        "priority" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "roomId" => self::TYPE_INTEGER,
        "category" => self::TYPE_INTEGER,
        "subject" => self::TYPE_STRING,
        "assetId" => self::TYPE_INTEGER,
        "creationDateTime" => self::TYPE_DATETIME,
        "lastActionDateTime" => self::TYPE_DATETIME,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "assignedToUser" => ["assignedToUserId" => \Database\Repository\User\User::class],
        "status" => ["status" => \Database\Repository\Helpdesk\Status::class],
        "priority" => ["priority" => \Database\Repository\Helpdesk\Priority::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "computer" => ['assetId' => \Database\Repository\Management\Computer::class],
        "category" => ['category' => \Database\Repository\Helpdesk\Category::class],
        "ipad" => ['assetId' => \Database\Repository\Management\IPad::class],
        "beamer" => ['assetId' => \Database\Repository\Management\Beamer::class],
        "printer" => ['assetId' => \Database\Repository\Management\Printer::class],
        "firewall" => ['assetId' => \Database\Repository\Management\Firewall::class],
        "switch" => ['assetId' => \Database\Repository\Management\MSwitch::class],
        "accesspoint" => ['assetId' => \Database\Repository\Management\AccessPoint::class]
    ];

    public function init()
    {
        $catRepo = new Category;

        $this->formatted->badge->status = $this->linked->status->formatted->badge->name;
        $this->formatted->badge->priority = $this->linked->priority->formatted->badge->name;
        $this->formatted->subject = Strings::equal($this->category, SELECT_OTHER_ID) ? ($this->subject ? $this->subject : SELECT_OTHER_VALUE) : $this->linked->category->formatted->name;

        // $_category = explode("-", $this->category);
        // $category = $catRepo->getByIdAndCategoryId($_category[0], null);
        // $this->formatted->subject = $category->name;
        // if (!is_null($_category[1])) $this->formatted->subject .= " - " . $catRepo->getByIdAndCategoryId($_category[1], $category->id)->name;
        // else if (Strings::equal($_category[0], "O")) $this->formatted->subject = $this->subject ?: $this->formatted->subject;

        // if (Strings::equal($_category[0], "L") || Strings::equal($_category[0], "D")) $this->formatted->subject = $this->linked->computer->name . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "I")) $this->formatted->subject = $this->linked->ipad->name . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "B")) $this->formatted->subject = $this->linked->beamer->serialnumber . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "P")) $this->formatted->subject = $this->linked->printer->name . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "F")) $this->formatted->subject = $this->linked->firewall->hostname . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "S")) $this->formatted->subject = $this->linked->switch->name . " - " . $this->formatted->subject;
        // else if (Strings::equal($_category[0], "A")) $this->formatted->subject = $this->linked->accesspoint->name . " - " . $this->formatted->subject;

        $this->formatted->link = "https://intranet.kaboe.be/helpdesk/mine/{$this->guid}";
        $this->formatted->assignedLink = "https://intranet.kaboe.be/helpdesk/assigned/{$this->guid}";

        $this->_lockedForm = (Strings::equal(User::getLoggedInUser()->id, $this->creatorUserId) && !Strings::equal(User::getLoggedInUser()->id, $this->assignedToUserId) || Strings::equal($this->status, 'C'));

        $this->createNumber();
        $this->createAge();
        $this->createLastActivity();
    }

    private function createNumber()
    {
        $this->formatted->number = (new Setting)->getByNavigationIdAndKey((new Navigation)->getByLink("helpdesk")->id, "format")->value;

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
        $la = Clock::at($this->lastActionDateTime)->toDateTime()->diff(Clock::now()->toDateTime());
        if ($la->y == 0 && $la->m == 0 && $la->d == 0 && $la->h == 0 && $la->i == 0) $this->la = $la->s . " seconden";
        else if ($la->y == 0 && $la->m == 0 && $la->d == 0 && $la->h == 0) $this->la = $la->i . " minuten";
        else if ($la->y == 0 && $la->m == 0 && $la->d == 0) $this->la = $la->h . " uren";
        else if ($la->y == 0 && $la->m == 0) $this->la = $la->d . " dagen";
        else if ($la->y == 0) $this->la = $la->m . " maanden";

        $this->formatted->lastActivity = new stdClass;
        $this->formatted->lastActivity->display = Clock::at($this->lastActionDateTime)->format("d/m/Y H:i:s") . " ({$this->la} geleden)";
        $this->formatted->lastActivity->sort = Clock::at($this->lastActionDateTime)->format("U");
    }
}
