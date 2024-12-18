<?php

namespace Database\Object;

use stdClass;
use Helpers\HTML;
use Security\Input;
use Helpers\CString;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Database\Repository\Navigation;
use Database\Interface\CustomObject;

class TempReg extends CustomObject
{
    protected $objectAttributes = [
        "id" => "int",
        "schoolId" => "int",
        "datetime" => "datetime",
        "name" => "string",
        "soup" => "double",
        "pasta" => "double",
        "vegetables" => "double",
        "meat" => "double",
        "notes" => "string",
        "deleted" => "boolean"
    ];

    protected $linkedAttributes = [
        "school" => [
            "schoolId" => \Database\Repository\School::class
        ]
    ];

    public function init()
    {
        $this->formatted->datetime = Clock::at($this->datetime)->format("d/m/Y H:i:s");
        $this->formatted->datetimeWithDay = new stdClass;
        $this->formatted->datetimeWithDay->display = Clock::at($this->datetime)->format("l d/m/Y H:i:s");
        $this->formatted->datetimeWithDay->sort = Clock::at($this->datetime)->format("U");

        $this->formatted->soup = CString::formatNumber($this->soup, 2) . "°C";
        $this->formatted->pasta = CString::formatNumber($this->pasta, 2) . "°C";
        $this->formatted->vegetables = CString::formatNumber($this->vegetables, 2) . "°C";
        $this->formatted->meat = CString::formatNumber($this->meat, 2) . "°C";

        $this->formatted->nameInitials = CString::firstLetterOfEachWord($this->name);

        $this->createBadgeSoup();
        $this->createBadgePasta();
        $this->createBadgeVegetables();
        $this->createBadgeMeat();
    }

    private function createBadgeSoup()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "tempreg"))->settings['treshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->soup <= $s['max'] && $this->soup >= $s['min']));

        $this->formatted->badge->soup = new stdClass;
        $this->formatted->badge->soup->display = $this->soup > 0 ? HTML::Badge($this->formatted->soup, backgroundColor: $level['color']) : "";
        $this->formatted->badge->soup->sort = $this->soup;
    }

    private function createBadgePasta()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "tempreg"))->settings['treshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->pasta <= $s['max'] && $this->pasta >= $s['min']));

        $this->formatted->badge->pasta = new stdClass;
        $this->formatted->badge->pasta->display = $this->pasta > 0 ? HTML::Badge($this->formatted->pasta, backgroundColor: $level['color']) : "";
        $this->formatted->badge->pasta->sort = $this->pasta;
    }

    private function createBadgeVegetables()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "tempreg"))->settings['treshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->vegetables <= $s['max'] && $this->vegetables >= $s['min']));

        $this->formatted->badge->vegetables = new stdClass;
        $this->formatted->badge->vegetables->display = $this->vegetables > 0 ? HTML::Badge($this->formatted->vegetables, backgroundColor: $level['color']) : "";
        $this->formatted->badge->vegetables->sort = $this->vegetables;
    }

    private function createBadgeMeat()
    {
        $settings = Arrays::first((new Navigation)->getByParentIdAndLink(0, "tempreg"))->settings['treshhold'];
        $level = Arrays::first(Arrays::filter($settings, fn($s) => $this->meat <= $s['max'] && $this->meat >= $s['min']));

        $this->formatted->badge->meat = new stdClass;
        $this->formatted->badge->meat->display = $this->meat > 0 ? HTML::Badge($this->formatted->meat, backgroundColor: $level['color']) : "";
        $this->formatted->badge->meat->sort = $this->meat;
    }
}
