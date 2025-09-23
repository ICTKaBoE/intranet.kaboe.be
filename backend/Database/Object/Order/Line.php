<?php

namespace Database\Object\Order;

use Helpers\CString;
use Ouzo\Utilities\Strings;
use Database\Interface\CustomObject;
use Database\Repository\Order\Category;
use Database\Repository\Navigation\Navigation;

class Line extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "orderId" => "int",
        "amount" => "int",
        "category" => "string",
        "assetId" => "int",
        "clarifycation" => "string",
        "quotePrice" => "double",
        "quoteVatIncluded" => "boolean",
        "warrenty" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        // "order" => ['orderId' => \Database\Repository\Order\Order::class],
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
        $catRepo = new Category;

        $this->formatted->quotePrice = ($this->warrenty ? "Garantie" : ($this->quotePrice == 0 ? "" : CString::formatCurrency($this->quotePrice) . " " . ($this->quoteVatIncluded ? 'incl.' : 'excl.') . ' btw'));

        $_category = explode("-", $this->category);
        $category = $catRepo->getByIdAndCategoryId($_category[0], null);
        $this->formatted->category = $category->name;
        if (!is_null($_category[1])) $this->formatted->category .= " - " . $catRepo->getByIdAndCategoryId($_category[1], $category->id)->name;

        if (Strings::equal($_category[0], "L") || Strings::equal($_category[0], "D")) $this->formatted->asset = "{$this->linked->computer->name} ({$this->linked->computer->formatted->manModel})";
        else if (Strings::equal($_category[0], "I")) $this->formatted->asset = "{$this->linked->ipad->name} ({$this->linked->ipad->model} / SN: {$this->linked->ipad->serialnumber})";
        else if (Strings::equal($_category[0], "B")) $this->formatted->asset = $this->linked->beamer->serialnumber;
        else if (Strings::equal($_category[0], "P")) $this->formatted->asset = "{$this->linked->printer->name} ({$this->linked->printer->formatted->manModel} / SN: {$this->linked->printer->serialnumber})";
        else if (Strings::equal($_category[0], "F")) $this->formatted->asset = "{$this->linked->firewall->hostname} ({$this->linked->firewall->formatted->manModel} / SN: {$this->linked->firewall->serialnumber})";
        else if (Strings::equal($_category[0], "S")) $this->formatted->asset = "{$this->linked->switch->name} ({$this->linked->switch->formatted->manModel} / SN: {$this->linked->switch->serialnumber})";
        else if (Strings::equal($_category[0], "A")) $this->formatted->asset = "{$this->linked->accesspoint->name} ({$this->linked->accesspoint->formatted->manModel} / SN: {$this->linked->accesspoint->serialnumber})";
    }
}
