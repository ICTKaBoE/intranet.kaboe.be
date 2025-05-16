<?php

namespace Controllers\API;

use Helpers\Date;
use Router\Helpers;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;
use Database\Repository\Management\Computer;
use Database\Repository\Management\ComputerUsageOnOff;
use Helpers\CString;

class ReportController extends ApiController
{
    public function getComputerUsageAmount($view, $id = null)
    {
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            'schoolyear' => Helpers::url()->getParam('schoolyear'),
            'type' => Helpers::url()->getParam('type')
        ];

        if ($filters['schoolId']) {
            $repo = new ComputerUsageOnOff;
            $computerRepo = new Computer;

            $start = General::getSchoolyearStartBySchoolyear($filters['schoolyear']);
            $end = General::getSchoolyearEndBySchoolyear($filters['schoolyear']);

            $computers = $computerRepo->getBySchoolIdAndLikeName($filters['schoolId'], $filters['type']);
            $items = [];

            foreach ($computers as $c) {
                $_items = $repo->getByComputerIdBetween($c->id, $start, $end);
                if (!$_items) continue;

                $_items = Arrays::filter($_items, fn($i) => !is_null($i->shutdown) || Strings::isNotBlank($i->shutdown));
                $items = Arrays::concat([$items, $_items]);
            }

            $_usagePerMonth = $usagePerDay = $usagePerWeek = [];
            foreach (Date::monthsBetweenDates($start, $end, "Y-m") as $m) $_usagePerMonth[$m] = 0;
            foreach ($items as $i) $_usagePerMonth[Clock::at($i->startup)->format("Y-m")]++;
            foreach ($_usagePerMonth as $k => $v) {
                $usagePerDay[] = ['x' => $k, 'y' => round($v / (Date::workingDays(Arrays::first(explode("-", $k)), Arrays::last(explode("-", $k))) * count($computers)), 2)];
                $usagePerWeek[] = ['x' => $k, 'y' => round($v / (4 * count($computers)), 2)];
            }

            // die(var_dump($usagePerMonth));
            $this->appendToJson(['series', 0], [
                'data' => $usagePerDay,
                'name' => 'Per dag'
            ]);
            $this->appendToJson(['series', 1], [
                'data' => $usagePerWeek,
                'name' => 'Per week'
            ]);
        }
    }

    public function getComputerUsageTime($view, $id = null)
    {
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            'schoolyear' => Helpers::url()->getParam('schoolyear'),
            'type' => Helpers::url()->getParam('type')
        ];

        if ($filters['schoolId']) {
            $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
            $repo = new ComputerUsageOnOff;
            $computerRepo = new Computer;

            $start = General::getSchoolyearStartBySchoolyear($filters['schoolyear']);
            $end = General::getSchoolyearEndBySchoolyear($filters['schoolyear']);

            $computers = $computerRepo->getBySchoolIdAndLikeName($filters['schoolId'], $filters['type']);
            $items = [];

            foreach ($computers as $c) {
                $_items = $repo->getByComputerIdBetween($c->id, $start, $end);
                if (!$_items) continue;

                $_items = Arrays::filter($_items, fn($i) => !is_null($i->shutdown) || Strings::isNotBlank($i->shutdown));
                $items = Arrays::concat([$items, $_items]);
            }

            $_usagePerMonth = $usagePerDay = $usagePerWeek = [];
            foreach (Date::monthsBetweenDates($start, $end, "Y-m") as $m) $_usagePerMonth[$m] = 0;
            foreach ($items as $i) $_usagePerMonth[Clock::at($i->startup)->format("Y-m")] += $i->seconds;
            foreach ($_usagePerMonth as $k => $v) {
                $usagePerDay[] = ['x' => $k, 'y' => round($v / (Date::workingDays(Arrays::first(explode("-", $k)), Arrays::last(explode("-", $k))) * count($computers)), 2)];
                $usagePerWeek[] = ['x' => $k, 'y' => round($v / (4 * count($computers)), 2)];
            }

            // die(var_dump($usagePerMonth));
            $this->appendToJson(['series', 0], [
                'data' => $usagePerDay,
                'name' => 'Per dag'
            ]);
            $this->appendToJson(['series', 1], [
                'data' => $usagePerWeek,
                'name' => 'Per week'
            ]);
        }
    }
}
