<?php

namespace Database\Object\StrategicDashboard;

use Helpers\CString;
use Helpers\General;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\CustomObject;
use Security\Input;

class Item extends CustomObject
{
    protected $objectAttributes = [
        "id" => self::TYPE_INTEGER,
        "schoolId" => self::TYPE_INTEGER,
        "typeId" => self::TYPE_INTEGER,
        "categoryId" => self::TYPE_INTEGER,
        "name" => self::TYPE_STRING,
        "minimum" => self::TYPE_DOUBLE,
        "target" => self::TYPE_DOUBLE,
        "canEditUserId" => self::TYPE_STRING,
        "width" => self::TYPE_INTEGER,
        "order" => self::TYPE_INTEGER,
        "info" => self::TYPE_STRING,
        "valueTemplate" => self::TYPE_ALL,
        "deleted" => self::TYPE_BOOLEAN
    ];

    protected $linkedAttributes = [
        "school" => ["schoolId" => \Database\Repository\School\School::class],
        "type" => ["typeId" => \Database\Repository\StrategicDashboard\ItemType::class],
        "category" => ["categoryId" => \Database\Repository\StrategicDashboard\Category::class]
    ];

    public function init()
    {
        $this->info = $this->info ?? "Geen extra informatie";
        $this->formatted->minimum = CString::formatNumber($this->minimum, 2);
        $this->formatted->target = CString::formatNumber($this->target, 2);
        $this->formatted->valueHtml = "";

        if (Strings::equal($this->linked->type->short, "number")) $this->formatted->html = "<span class='fs-1'>@formatted.value@</span>";
        else if (Strings::equal($this->linked->type->short, "rating:circle")) $this->formatted->html = "<div role='rating' id='rtn{$this->id}' data-icon='circle' data-color='white' data-size='1' data-value='@value@'></div>";
        else if (Strings::equal($this->linked->type->short, "multirating:circle")) {
            $rows = json_decode($this->valueTemplate, true) ?? [];
            foreach ($rows as $k => $v) $this->formatted->html .= "<h1>{$k}</h1><div class='mb-3' role='rating' id='rtn{$this->id}{$k}' data-icon='circle' data-color='white' data-size='1' data-value='@value_{$k}@'></div>";
        } else if (Strings::equal($this->linked->type->short, "chart:pie")) $this->formatted->html = "<div role='chart' data-type='donut' id='crt{$this->id}' data-height='300vh' data-source='https://" . (DEV_MODE ? "dev." : "") . "api.kaboe.be/chart/strategicDashboard/dashboard/{$this->id}?type=pie'></div>";
        else if (Strings::equal($this->linked->type->short, "chart:bar")) $this->formatted->html = "<div role='chart' data-type='bar' id='crt{$this->id}' data-height='200vh' data-source='https://" . (DEV_MODE ? "dev." : "") . "api.kaboe.be/chart/strategicDashboard/dashboard/{$this->id}?type=bar'></div>";

        if (Arrays::contains(["number", "rating:circle"], $this->linked->type->short))
            $this->formatted->valueHtml = "
                <div class='col-12 mb-3'>
                    <label class='form-label' for='value'>Waarde</label>
                    <input type='number' class='form-control' id='value' name='value' />
                </div>
            ";
        else if (Arrays::contains(["chart:pie", "chart:bar", "multirating:circle"], $this->linked->type->short)) {
            $template = json_decode(str_replace(PHP_EOL, "", $this->valueTemplate), true);

            foreach ($template as $k => $v) {
                if (Strings::startsWith($k, "LOOP:")) {
                    [$rep, $att] = explode("@", str_replace("LOOP:", "", $k));

                    foreach ((new $rep)->get() as $i)
                        $this->formatted->valueHtml .= "
                            <div class='col-lg-6 col-12 mb-3'>
                                <label class='form-label' for='{$i->$att}'>{$i->$att}</label>
                                <input type='number' class='form-control' id='{$i->$att}' name='{$i->$att}' />
                            </div>
                        ";
                } else {
                    $this->formatted->valueHtml .= "
                        <div class='col-lg-6 col-12 mb-3'>
                            <label class='form-label' for='{$k}'>{$k}</label>
                            <input type='number' class='form-control' id='{$k}' name='{$k}' />
                        </div>
                    ";
                }
            }
        }
    }
}
