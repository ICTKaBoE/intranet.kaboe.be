<?php

namespace Controllers\API;

use Helpers\Date;
use Router\Helpers;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Helpdesk\Helpdesk;
use Database\Repository\Helpdesk\Status;
use Database\Repository\Management\Computer;
use Database\Repository\Management\ComputerUsageOnOff;
use Database\Repository\Registration\Schoolyear;
use Database\Repository\TempReg\TempReg;
use Database\Repository\TempReg\Treshhold;

class ReportController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "report";

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
            $schoolyear = (new Schoolyear)->getByName($filters['schoolyear']);

            $computers = $computerRepo->getBySchoolIdAndLikeName($filters['schoolId'], $filters['type']);
            $items = [];

            foreach ($computers as $c) {
                $_items = $repo->getByComputerIdBetween($c->id, $schoolyear->start, $schoolyear->end);
                if (!$_items) continue;

                $_items = Arrays::filter($_items, fn($i) => !is_null($i->shutdown) || Strings::isNotBlank($i->shutdown));
                $items = Arrays::concat([$items, $_items]);
            }

            $_usagePerMonth = $usagePerDay = $usagePerWeek = [];
            foreach (Date::monthsBetweenDates($schoolyear->start, $schoolyear->end, "Y-m") as $m) $_usagePerMonth[$m] = 0;
            foreach ($items as $i) $_usagePerMonth[Clock::at($i->startup)->format("Y-m")]++;
            foreach ($_usagePerMonth as $k => $v) {
                $usagePerDay[] = ['x' => $k, 'y' => round($v / (Date::workingDays(Arrays::first(explode("-", $k)), Arrays::last(explode("-", $k))) * count($computers)), 2)];
                $usagePerWeek[] = ['x' => $k, 'y' => round($v / (4 * count($computers)), 2)];
            }

            // die(var_dump($usagePerMonth));
            $this->appendToJson('series', [
                [
                    'data' => $usagePerDay,
                    'name' => 'Per dag'
                ],
                [
                    'data' => $usagePerWeek,
                    'name' => 'Per week'
                ]
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
            $repo = new ComputerUsageOnOff;
            $computerRepo = new Computer;
            $schoolyear = (new Schoolyear)->getByName($filters['schoolyear']);

            $computers = $computerRepo->getBySchoolIdAndLikeName($filters['schoolId'], $filters['type']);
            $items = [];

            foreach ($computers as $c) {
                $_items = $repo->getByComputerIdBetween($c->id, $schoolyear->start, $schoolyear->end);
                if (!$_items) continue;

                $_items = Arrays::filter($_items, fn($i) => !is_null($i->shutdown) || Strings::isNotBlank($i->shutdown));
                $items = Arrays::concat([$items, $_items]);
            }

            $_usagePerMonth = $usagePerDay = $usagePerWeek = [];
            foreach (Date::monthsBetweenDates($schoolyear->start, $schoolyear->end, "Y-m") as $m) $_usagePerMonth[$m] = 0;
            foreach ($items as $i) $_usagePerMonth[Clock::at($i->startup)->format("Y-m")] += $i->seconds;
            foreach ($_usagePerMonth as $k => $v) {
                $usagePerDay[] = ['x' => $k, 'y' => round($v / (Date::workingDays(Arrays::first(explode("-", $k)), Arrays::last(explode("-", $k))) * count($computers)), 2)];
                $usagePerWeek[] = ['x' => $k, 'y' => round($v / (4 * count($computers)), 2)];
            }

            // die(var_dump($usagePerMonth));
            $this->appendToJson('series', [
                [
                    'data' => $usagePerDay,
                    'name' => 'Per dag'
                ],
                [
                    'data' => $usagePerWeek,
                    'name' => 'Per week'
                ]
            ]);
        }
    }

    public function getTempreg($view, $id = null)
    {
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
            'schoolyear' => Helpers::url()->getParam('schoolyear'),
        ];

        if ($filters['schoolId']) {
            $repo = new TempReg;
            $schoolyear = (new Schoolyear)->getByName($filters['schoolyear']);
            $items = $repo->getBySchoolIdBetween($filters['schoolId'], $schoolyear->start, $schoolyear->end);
            $items = Arrays::orderBy($items, 'datetime');

            $soup = $pasta = $vegetables = $meat = [];
            foreach ($items as $item) {
                $soup[] = [
                    'x' => Clock::at($item->datetime)->format("Y-m-d"),
                    'y' => $item->soup == 0 ? null : $item->soup
                ];
                $pasta[] = [
                    'x' => Clock::at($item->datetime)->format("Y-m-d"),
                    'y' => $item->pasta == 0 ? null : $item->pasta
                ];
                $vegetables[] = [
                    'x' => Clock::at($item->datetime)->format("Y-m-d"),
                    'y' => $item->vegetables == 0 ? null : $item->vegetables
                ];
                $meat[] = [
                    'x' => Clock::at($item->datetime)->format("Y-m-d"),
                    'y' => $item->meat == 0 ? null : $item->meat
                ];
            }

            $this->appendToJson('series', [
                [
                    'data' => $soup,
                    'name' => 'Soep'
                ],
                [
                    'data' => $pasta,
                    'name' => 'Aardappel, pasta, rijst, ...'
                ],
                [
                    'data' => $vegetables,
                    'name' => 'Groente'
                ],
                [
                    'data' => $meat,
                    'name' => 'Vlees/Vis'
                ]
            ]);

            $treshholds = Arrays::map((new Treshhold)->get(), fn($t) => [
                'label' => ['text' => ''],
                'y' => $t->min,
                'y2' => $t->max,
                'fillColor' => $t->color
            ]);
            $this->appendToJson(['options', 'annotations', 'yaxis'], $treshholds);
        }
    }

    public function getHelpdesk($view, $id = null)
    {
        $statusses = (new Status)->get();
        $labels = array_values(Arrays::map($statusses, fn($s) => $s->name));
        $series = [];

        $ticketRepo = new Helpdesk;

        foreach ($statusses as $status) {
            $series[] = count($ticketRepo->getByStatus($status->id));
        }

        $this->appendToJson(['options', 'labels'], $labels);
        $this->appendToJson('series', $series);
    }
}
