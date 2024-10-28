<?php

namespace Database\Object\Order;

use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;
use Helpers\CString;

class PurchaseLine extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "purchaseId" => "int",
        "amount" => "int",
        "category" => "string",
        "assetId" => "int",
        "clarifycation" => "string",
        "quotePrice" => "double",
        "quoteVatIncluded" => "boolean",
        "warrenty" => "boolean",
        "accepted" => "boolean",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "purchase" => ['purchaseId' => \Database\Repository\Order\Purchase::class],
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

        $this->formatted->quotePrice = ($this->warrenty ? "Garantie" : ($this->quotePrice == 0 ? "" : CString::formatCurrency($this->quotePrice) . " " . ($this->quoteVatIncluded ? 'incl.' : 'excl.') . ' btw'));
        $this->formatted->icon->accepted = ($this->quotePrice == 0 ? "" : "<i class='ti ti-" . ($this->accepted ? 'check' : 'x') . "' title='" . ($this->accepted ? 'Goedgekeurd' : 'Afgekeurd') . "'></i>");

        $category = explode("-", $this->category);
        $this->formatted->subject = $settings['category'][$category[0]]['name'];
        if (isset($category[1])) $this->formatted->subject .= " - " . $settings['category'][$category[0]]['sub'][$category[1]];

        if (Strings::equal($category[0], "L") || Strings::equal($category[0], "D")) $this->formatted->asset = "{$this->linked->computer->name} ({$this->linked->computer->formatted->manModel})";
        else if (Strings::equal($category[0], "I")) $this->formatted->asset = "{$this->linked->ipad->name} ({$this->linked->ipad->model} / SN: {$this->linked->ipad->serialnumber})";
        else if (Strings::equal($category[0], "B")) $this->formatted->asset = $this->linked->beamer->serialnumber;
        else if (Strings::equal($category[0], "P")) $this->formatted->asset = "{$this->linked->printer->name} ({$this->linked->printer->formatted->manModel} / SN: {$this->linked->printer->serialnumber})";
        else if (Strings::equal($category[0], "F")) $this->formatted->asset = "{$this->linked->firewall->hostname} ({$this->linked->firewall->formatted->manModel} / SN: {$this->linked->firewall->serialnumber})";
        else if (Strings::equal($category[0], "S")) $this->formatted->asset = "{$this->linked->switch->name} ({$this->linked->switch->formatted->manModel} / SN: {$this->linked->switch->serialnumber})";
        else if (Strings::equal($category[0], "A")) $this->formatted->asset = "{$this->linked->accesspoint->name} ({$this->linked->accesspoint->formatted->manModel} / SN: {$this->linked->accesspoint->serialnumber})";
    }
}
