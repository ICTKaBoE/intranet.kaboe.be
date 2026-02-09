<?php

namespace Database\Object\Order;

use Helpers\CString;
use Ouzo\Utilities\Strings;
use Security\CustomObject;

class Line extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "orderId" => self::TYPE_INTEGER,
        "amount" => self::TYPE_INTEGER,
        "category" => self::TYPE_STRING,
        "assetId" => self::TYPE_INTEGER,
        "clarifycation" => self::TYPE_STRING,
        "quotePrice" => self::TYPE_DOUBLE,
        "quoteVatIncluded" => self::TYPE_BOOLEAN,
        "warrenty" => self::TYPE_BOOLEAN,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "category" => ['category' => \Database\Repository\Order\Category::class],
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
        $this->formatted->quotePrice = ($this->warrenty ? "Garantie" : ($this->quotePrice == 0 ? "" : CString::formatCurrency($this->quotePrice) . " " . ($this->quoteVatIncluded ? 'incl.' : 'excl.') . ' btw'));
        $this->formatted->category = Strings::equal($this->category, SELECT_OTHER_ID) ? SELECT_OTHER_VALUE : $this->linked->category->formatted->name;

        if (Strings::equal($this->linked->category->managementType, "L") || Strings::equal($this->linked->category->managementType, "D")) $this->formatted->asset = "{$this->linked->computer->name} ({$this->linked->computer->formatted->manModel})";
        else if (Strings::equal($this->linked->category->managementType, "I")) $this->formatted->asset = "{$this->linked->ipad->name} ({$this->linked->ipad->model} / SN: {$this->linked->ipad->serialnumber})";
        else if (Strings::equal($this->linked->category->managementType, "B")) $this->formatted->asset = $this->linked->beamer->serialnumber;
        else if (Strings::equal($this->linked->category->managementType, "P")) $this->formatted->asset = "{$this->linked->printer->name} ({$this->linked->printer->formatted->manModel} / SN: {$this->linked->printer->serialnumber})";
        else if (Strings::equal($this->linked->category->managementType, "F")) $this->formatted->asset = "{$this->linked->firewall->hostname} ({$this->linked->firewall->formatted->manModel} / SN: {$this->linked->firewall->serialnumber})";
        else if (Strings::equal($this->linked->category->managementType, "S")) $this->formatted->asset = "{$this->linked->switch->name} ({$this->linked->switch->formatted->manModel} / SN: {$this->linked->switch->serialnumber})";
        else if (Strings::equal($this->linked->category->managementType, "A")) $this->formatted->asset = "{$this->linked->accesspoint->name} ({$this->linked->accesspoint->formatted->manModel} / SN: {$this->linked->accesspoint->serialnumber})";
    }
}
