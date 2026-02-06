<?php

namespace Database\Object\Order;

use Helpers\HTML;
use Router\Helpers;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use Database\Repository\Order\Status;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Navigation\Setting;

class Order extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "guid" => self::TYPE_GUID,
        "schoolId" => self::TYPE_INTEGER,
        "creatorUserId" => self::TYPE_INTEGER,
        "acceptorUserId" => self::TYPE_STRING,
        "supplierId" => self::TYPE_INTEGER,
        "quoteLink" => self::TYPE_STRING,
        "quoteFile" => self::TYPE_STRING,
        "orderNumber" => self::TYPE_STRING,
        "status" => self::TYPE_STRING,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "creatorUser" => ["creatorUserId" => \Database\Repository\User\User::class],
        "acceptorUser" => ["acceptorUserId" => \Database\Repository\User\User::class],
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "supplier" => ['supplierId' => \Database\Repository\Order\Supplier::class]
    ];

    public function init()
    {
        $status = (new Status)->getById($this->status);

        $this->formatted->badge->status = $status->formatted->badge->name;
        $this->formatted->acceptor = is_array($this->linked->acceptorUser) ? join('<br />', Arrays::map($this->linked->acceptorUser, fn($a) => $a->formatted->fullName)) : $this->linked->acceptorUser->formatted->fullName;

        $this->formatted->link = (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost() . "/order/accept/{$this->guid}";
        $this->_lockedForm = !Arrays::contains(["N", "WQ"], $this->status);

        $this->createNumber();
    }

    private function createNumber()
    {
        $settings = (new Navigation)->getByParentIdAndLink(0, "order");
        $this->formatted->number = (new Setting)->getByNavigationIdAndKey($settings->id, "format")->value;

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
}
