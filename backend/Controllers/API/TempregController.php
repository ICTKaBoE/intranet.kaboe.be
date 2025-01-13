<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Excel;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\School;
use Database\Repository\TempReg;
use Database\Repository\Navigation;
use Database\Repository\Informat\Teacher;
use Database\Object\TempReg as ObjectTempReg;

class TempregController extends ApiController
{
    // Get Functions
    protected function getPerson($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        if (Strings::equal($view, self::VIEW_SELECT)) {
            if (Helpers::url()->getParam("schoolId")) {
                $school = str_replace([" ", "-"], "", (new School)->get(Helpers::url()->getParam('schoolId'))[0]->name);
                $who = $_settings['who'][$school];

                $names = explode(PHP_EOL, $who);
                if (!is_array($names)) $names = [$names];

                $names = Arrays::map($names, fn($n) => ["name" => $n]);
                $names = Arrays::orderBy($names, "name");

                $this->appendToJson('items', $names);
            } else $this->appendToJson('items', []);
        }
    }

    protected function getOverview($view, $id = null)
    {
        $repo = new TempReg;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam("schoolId")), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[1, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Datum/Tijd",
                        "data" => "formatted.datetimeWithDay",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "300px",
                    ],
                    [
                        "title" => "Gemeten door",
                        "data" => "name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Soep",
                        "data" => "formatted.badge.soup",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "100px",
                    ],
                    [
                        "title" => "Aardappel/Pasta/Rijst",
                        "data" => "formatted.badge.pasta",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "100px",
                    ],
                    [
                        "title" => "Groenten",
                        "data" => "formatted.badge.vegetables",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "100px",
                    ],
                    [
                        "title" => "Vlees/Vis",
                        "data" => "formatted.badge.meat",
                        "render" => [
                            "_" => "display",
                            "sort" => "sort"
                        ],
                        "width" => "100px",
                    ],
                    [
                        "title" => "Opmerkingen",
                        "data" => "notes"
                    ],
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);
            if (Helpers::url()->getParam("start")) $items = Arrays::filter($items, fn($i) => Clock::at($i->start)->isAfterOrEqualTo(Clock::at(Helpers::url()->getParam("start"))));
            if (Helpers::url()->getParam("end")) $items = Arrays::filter($items, fn($i) => Clock::at($i->end)->isBeforeOrEqualTo(Clock::at(Helpers::url()->getParam("end"))));

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getSettings($view, $id = null)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    // Post functions
    protected function postAdd($view, $id)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $soup = Helpers::input()->post('soup')->getValue();
        $pasta = Helpers::input()->post('pasta')->getValue();
        $vegetables = Helpers::input()->post('vegetables')->getValue();
        $meat = Helpers::input()->post('meat')->getValue();
        $notes = Helpers::input()->post('notes')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new TempReg;
            $item = new ObjectTempReg;
            $item->schoolId = $schoolId;
            $item->name = $name;
            $item->soup = $soup;
            $item->pasta = $pasta;
            $item->vegetables = $vegetables;
            $item->meat = $meat;
            $item->notes = $notes;

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De registratie is opgeslagen!");
            $this->setResetForm();
        }
    }

    protected function postSettings($view, $id = null)
    {
        $_settings = Helpers::input()->all();
        $settings = [];
        foreach ($_settings as $k => $v) $settings[str_replace("_", ".", $k)] = $v;
        $settings = General::normalizeArray($settings);

        $repo = new Navigation;
        $item = Arrays::first($repo->get(Session::get("moduleSettingsId")));
        $item->settings = json_encode(array_replace_recursive($item->settings, $settings));

        $repo->set($item, ['settings']);
        $this->setToast("De instellingen zijn opgeslagen!");
    }

    protected function postExport($view, $id = null)
    {
        $school = Helpers::input()->post('school');
        if (!is_null($school)) $school = $school->getValue();
        if (is_array($school) && !Strings::contains($school, ";")) {
            $s = [];
            foreach ($school as $sch) {
                $s[] = $sch->getValue();
            }
            $school = $s;
        } else if (Strings::contains($school, ";")) {
            $school = explode(";", $school);
        } else $school = [$school];
        $start = Helpers::input()->post('start')->getValue();
        $end = Helpers::input()->post('end')->getValue();
        $showNamesAs = Helpers::input()->post('showNamesAs')->getValue();
        $exportAs = Helpers::input()->post('exportAs')->getValue();

        if (Input::empty($school[0])) $this->setValidation("school", "Scholen moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($start) || Input::empty($start)) $this->setValidation("start", "Start datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($end) || Input::empty($end)) $this->setValidation("end", "Eind datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            if (Clock::at($start)->isAfter(Clock::at($end))) {
                $this->setValidation("start", "Start datum moet voor de eind datum liggen!", self::VALIDATION_STATE_INVALID);
                $this->setValidation("end", "Start datum moet voor de eind datum liggen!", self::VALIDATION_STATE_INVALID);
            }

            if ($this->validationIsAllGood()) {
                if (Strings::equal($exportAs, 'pdf')) $this->exportPerSchoolAsPdf($school, $start, $end, $showNamesAs);
                else if (Strings::equal($exportAs, 'xlsx')) $this->exportPerSchoolAsXlsx($school, $start, $end, $showNamesAs);
                $this->setValidation("start", "", self::VALIDATION_STATE_VALID);
                $this->setValidation("end", "", self::VALIDATION_STATE_VALID);
            }
        }
    }

    // export functions
    protected function exportPerSchoolAsPdf($schoolIds, $start, $end, $showNamesAs)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $zipFileName = "Temperatuurregistratie - Export Per School.zip";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        foreach ($schoolIds as $index => $schoolId) {
            $groupedEvents = $this->getEventsGroupedByDateBySchoolId($schoolId, $start, $end, $showNamesAs);

            $school = $schoolRepo->get($schoolId)[0];
            $pdf = new PDF($school->name, "{$folder}/{$school->name}.pdf", "L", "Temperatuurregistratie warme/koude maaltijd - Overzicht: {$school->name}");

            foreach ($monthsBetweenDates as $month) {
                $pdf->AddPage();
                $pdf->Cell(20, 10, 'Maand:', ln: 0, align: 'L', calign: 'C', valign: 'C');
                $pdf->Cell(0, 10, $month, ln: 1, align: 'L', calign: 'C', valign: 'C');

                $table = [];
                $table['header'][] = [
                    "title" => "Datum/Tijd",
                    "border" => "B",
                    "width" => 70
                ];

                $table['header'][] = [
                    "title" => "Soep",
                    "border" => "LB",
                    "width" => 25
                ];

                $table['header'][] = [
                    "title" => "Aardappel/pasta/rijst",
                    "border" => "LB",
                    "width" => 50
                ];

                $table['header'][] = [
                    "title" => "Groente",
                    "border" => "LB",
                    "width" => 25
                ];

                $table['header'][] = [
                    "title" => "Vlees/vis",
                    "border" => "LB",
                    "width" => 25
                ];

                $table['header'][] = [
                    "title" => "Naam" . ($showNamesAs == "initials" ? " (initialen)" : ""),
                    "border" => "LB",
                    "width" => 40
                ];

                $table['header'][] = [
                    "title" => "Opmerkingen",
                    "border" => "LB",
                    "width" => 35
                ];

                foreach ($groupedEvents as $user => $events) {
                    if (array_keys($events)[0] == $month) {
                        $table['data'][$user][] = [
                            "text" => $events[$month]['datetime'],
                            "border" => "T"
                        ];

                        if (!is_null($events[$month]['soup'])) {
                            $table['data'][$user][] = [
                                "text" => $events[$month]['soup'],
                                "border" => "LT"
                            ];
                        } else $table['data'][$user][] = ["border" => "LT"];

                        if (!is_null($events[$month]['pasta'])) {
                            $table['data'][$user][] = [
                                "text" => $events[$month]['pasta'],
                                "border" => "LT"
                            ];
                        } else $table['data'][$user][] = ["border" => "LT"];

                        if (!is_null($events[$month]['vegetables'])) {
                            $table['data'][$user][] = [
                                "text" => $events[$month]['vegetables'],
                                "border" => "LT"
                            ];
                        } else $table['data'][$user][] = ["border" => "LT"];

                        if (!is_null($events[$month]['meat'])) {
                            $table['data'][$user][] = [
                                "text" => $events[$month]['meat'],
                                "border" => "LT"
                            ];
                        } else $table['data'][$user][] = ["border" => "LT"];

                        $table['data'][$user][] = [
                            "text" => $events[$month]['name'],
                            "border" => "LT"
                        ];

                        $table['data'][$user][] = [
                            "text" => $events[$month]['notes'],
                            "border" => "LT"
                        ];
                    }
                }
                $pdf->Ln(10);
                $pdf->table($table);
            }
            $pdf->save();
        }

        $zipFile = new ZIP("{$folder}/{$zipFileName}");
        $zipFile->addDir($folder);
        $zipFile->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$zipFileName}"));
    }

    protected function exportPerSchoolAsXlsx($schoolIds, $start, $end, $showNamesAs)
    {
        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Temperatuurregistratie - Export Per School.xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        $startRow = 5;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");
        foreach ($schoolIds as $index => $schoolId) {
            $groupedEvents = $this->getEventsGroupedByDateBySchoolId($schoolId, $start, $end, $showNamesAs);

            $school = $schoolRepo->get($schoolId)[0];
            $excel->createSheet($index, $school->name);
            $excel->setCellValue($index, "A1:P1", "Temperatuurregistratie warme/koude maaltijd - {$school->name}", true, 14);
            $excel->setCellValue($index, "A2", "Startdatum");
            $excel->setCellValue($index, "B2", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A3", "Einddatum");
            $excel->setCellValue($index, "B3", Clock::at($end)->format("d/m/Y"));

            $schoolRow = $startRow;

            foreach ($monthsBetweenDates as $month) {
                $schoolColumn = $startColumn;
                $status = 1;

                foreach ($groupedEvents as $user => $events) {

                    if (array_keys($events)[0] == $month && !empty(array_values($events)[0])) {

                        if ($status == 1) {

                            $schoolRow++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "{$month}", true, 13, border: "lbrt");
                            $schoolRow++;

                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Datum/Tijd", true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Soep", true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Aardappel/pasta/rijst", true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Groente", true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Vlees/vis", true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Naam" . ($showNamesAs == "initials" ? " (initialen)" : ""), true, border: "b");
                            $schoolColumn++;
                            $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Opmerkingen", true, border: "b");

                            $schoolRow++;
                            $status++;
                        }

                        $schoolColumn = $startColumn;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['datetime'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['soup'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['pasta'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['vegetables'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['meat'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['name'], border: "t");
                        $schoolColumn++;
                        $excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['notes'], border: "t");

                        $schoolRow++;
                    }
                }
            }
        }

        $excel->removeSheetByName('Worksheet');
        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    protected function getEventsGroupedByDateBySchoolId($schoolId, $start, $end, $showNamesAs)
    {
        $eventRepo = new TempReg;
        $eventsGrouped = [];

        $events = $eventRepo->getBySchoolId($schoolId);
        $events = Arrays::filter($events, fn($e) => Clock::at($e->datetime)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->datetime)->isBeforeOrEqualTo(Clock::at($end)));

        foreach ($events as $event) {
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['datetime'] = $event->formatted->datetimeWithDay->display;
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['soup'] = $event->formatted->soup;
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['pasta'] = $event->formatted->pasta;
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['vegetables'] = $event->formatted->vegetables;
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['meat'] = $event->formatted->meat;
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['name'] = ($showNamesAs == "initials" ? $event->formatted->nameInitials : $event->name);
            $eventsGrouped[$event->id][Clock::at($event->datetime)->format("F Y")]['notes'] = $event->notes;
        }

        return $eventsGrouped;
    }
}
