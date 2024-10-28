<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Excel;
use Security\GUID;
use Security\User;
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
use Database\Repository\BikeEvent;
use Database\Repository\Navigation;
use Database\Repository\UserAddress;
use Database\Repository\BikeDistance;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Database\Repository\User as RepositoryUser;
use Database\Object\BikeEvent as ObjectBikeEvent;
use Database\Object\BikeDistance as ObjectBikeDistance;
use Database\Repository\BikePrice;

class BikeController extends ApiController
{
    public function get($view, $what = null, $id = null)
    {
        if (Strings::equal($what, "distance")) $this->getDistance($view, $id);
        else if (Strings::equal($what, "home-work")) $this->getEvent($view, $id, 'HW');
        else if (Strings::equal($what, "work-work")) $this->getEvent($view, $id, 'WW');
        else if (Strings::equal($what, "settings")) $this->getSettings($view);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function post($view, $what, $id = null)
    {
        if (Strings::equal($what, "distance")) $this->postDistance($id);
        else if (Strings::equal($what, "home-work")) $this->postEvent("HW");
        else if (Strings::equal($what, "work-work")) $this->postEvent("WW");
        else if (Strings::equal($what, "settings")) $this->postSettings();
        else if (Strings::equal($what, "export")) $this->postExport();

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        $this->handle();
    }

    public function delete($view, $what, $id = null)
    {
        if (Strings::equal($what, "distance")) $this->deleteDistance($id);

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
        }
        $this->handle();
    }

    // Get Functions
    private function getDistance($view, $id)
    {
        $repo = new BikeDistance;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, "table")) {
            $filters = [
                'type' => Helpers::url()->getParam("type"),
                'startId' => Helpers::url()->getParam('startId')
            ];

            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[2, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "data" => "formatted.badge.color",
                        "width" => "20px",
                        "orderable" => false,
                        "searchable" => false
                    ],
                    [
                        "title" => "Alias",
                        "data" => "alias",
                        "width" => "10%",
                        "priority" => 1
                    ],
                    [
                        "title" => "Type",
                        "data" => "mapped.type",
                        "width" => "10%",
                        "priority" => 2
                    ],
                    [
                        "title" => "Start Locatie",
                        "data" => "startAddress",
                    ],
                    [
                        "title" => "Eindbestemming",
                        "data" => "linked.endSchool.name",
                        "width" => "10%",
                        "priority" => 4
                    ],
                    [
                        "type" => "double",
                        "title" => "Afstand",
                        "data" => "formatted.distanceWithDouble",
                        "width" => "10%",
                        "priority" => 3
                    ]
                ]
            );

            $distances = $repo->getByUserId($currentUserId);

            foreach ($filters as $key => $value) {
                if (!$value) continue;
                $distances = Arrays::filter($distances, fn($d) => Strings::equal($d->$key, $value));
            }

            $this->appendToJson("rows", array_values($distances));
        } else if (Strings::equal($view, "select")) {
        } else if (Strings::equal($view, "form")) $this->appendToJson('fields', $repo->get($id)[0]);
        else if (Strings::equal($view, "list")) {
            $type = Helpers::input()->get('type')->getValue();
            $this->appendToJson('items', $repo->getByUserIdAndType($currentUserId, $type));
        }
    }

    private function getEvent($view, $id, $type)
    {
        $repo = new BikeEvent;
        $currentUserId = User::getLoggedInUser()->id;

        if (Strings::equal($view, "calendar")) {
            $items = $repo->getByUserIdAndType($currentUserId, $type);
            $items = Arrays::filter($items, fn($i) => $i->distance > 0);

            foreach ($items as $event) {
                $this->appendToJson(data: [
                    "start" => $event->date,
                    "title" => "{$event->alias} ({$event->formatted->distanceWithDouble})",
                    "display" => "background",
                    "classNames" => [
                        "bg-{$event->color}",
                        "text-{$event->textColor}"
                    ],
                    "allDay" => true,
                ]);
            }
        }
    }

    private function getSettings($view)
    {
        $repo = new Navigation;
        $_settings = Arrays::first($repo->get(Session::get("moduleSettingsId")))->settings;

        $this->appendToJson('fields', Arrays::flattenKeysRecursively($_settings));
    }

    // Post Functions
    private function postDistance($id = null)
    {
        if ($id == "add") $id = null;

        $alias = Helpers::input()->post('alias')->getValue();
        $type = Helpers::input()->post('type')->getValue();
        $startId = Helpers::input()->post('startId')->getValue();
        $endSchoolId = Helpers::input()->post('endSchoolId')->getValue();
        $distance = Helpers::input()->post('distance')->getValue();
        $color = Helpers::input()->post('color')->getValue();

        if (!Input::check($alias) || Input::empty($alias)) $this->setValidation("alias", "Alias moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($type) || Input::empty($type)) $this->setValidation("type", "Type adres moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($startId) || Input::empty($startId)) $this->setValidation("startId", "Startlocatie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($endSchoolId) || Input::empty($endSchoolId)) $this->setValidation("endSchoolId", "Eindbestemming moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($distance) || Input::empty($distance)) $this->setValidation("distance", "Afstand moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($color) || Input::empty($color)) $this->setValidation("color", "Kleur moet aangeduid zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new BikeDistance;

            foreach ($repo->getByUserId(User::getLoggedInUser()->id) as $_distance) {
                if (Strings::equal($_distance->id, $id) || Strings::equal($_distance->guid, $id)) continue;

                if (Strings::equal($_distance->alias, $alias)) $this->setValidation("alias", "Er bestaat al een afstand met alias '{$alias}'!", self::VALIDATION_STATE_INVALID);
                if (Strings::equal($_distance->type, $type) && Strings::equal($_distance->startId, $startId) && Strings::equal($_distance->endSchoolId, $endSchoolId)) $this->setValidation("startId", "Er bestaat al een rit met hetzelfde startlocatie en school!", self::VALIDATION_STATE_INVALID);
                if (!$this->validationIsAllGood()) break;
            }

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new ObjectBikeDistance;
                $item->userId = User::getLoggedInUser()->id;
                $item->alias = $alias;
                $item->type = $type;
                $item->startId = $startId;
                $item->endSchoolId = $endSchoolId;
                $item->distance = $distance;
                $item->color = $color;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De afstand is opgeslagen!");
            $this->setReturn();
        }
    }

    private function postEvent($type)
    {
        $date = Helpers::input()->post('date')->getValue();
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;

        if ($settings['block']['past']['enabled']) {
            $pastDate = Clock::now()->toDateTime();
            if ($settings['block']['past']['amount'] !== 0) $pastDate->modify("-" . $settings['block']['past']['amount']);
            $pastDate = Clock::at($pastDate->format('Y-m-d'));

            if ($settings['lastPayDate'] && $pastDate->isBeforeOrEqualTo(Clock::at($settings['lastPayDate']))) $pastDate = clock::at($settings['lastPayDate']);

            if (Clock::at($date)->isBefore($pastDate)) $this->setToast("U kan geen rit inboeken voor {$pastDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
        }

        if ($settings['block']['future']['enabled']) {
            $futureDate = Clock::now()->toDateTime();
            if ($settings['block']['future']['amount'] !== 0) $futureDate->modify("+" . $settings['block']['future']['amount']);
            $futureDate = Clock::at($futureDate->format('Y-m-d'));

            if (Clock::at($date)->isAfter($futureDate)) $this->setToast("U kan geen rit inboeken na {$futureDate->format('d/m/Y')}", self::VALIDATION_STATE_INVALID);
        }

        if ($this->validationIsAllGood()) {
            $rDate = Clock::at($date)->format("d/m/Y");

            $currentUserId = User::getLoggedInUser()->id;
            $repo = new BikeEvent;
            $dRepo = new BikeDistance;

            $item = $repo->getByUserIdTypeAndDate($currentUserId, $type, $date) ?? new ObjectBikeEvent;
            $distances = $dRepo->getByUserIdAndType($currentUserId, $type);

            $distance = null;
            if ($item->bikeDistanceId == null) $distance = $distances[0];
            else {
                $break = false;
                foreach ($distances as $i => $d) {
                    if ($break) {
                        $distance = $d;
                        break;
                    }

                    if ($i + 1 == count($distances)) $distance = null;
                    else if ($d->id == $item->bikeDistanceId) $break = true;
                }
            }

            $item->date = $date;
            $item->bikeDistanceId = $distance->id;
            $item->type = $type;
            $item->userId = $currentUserId;
            $item->startId = $distance->startId;
            $item->endSchoolId = $distance->endSchoolId;
            $item->distance = $distance->distance;
            $item->alias = $distance->alias;
            $item->color = $distance->color;
            $item->userMainSchoolId = User::getLoggedInUser()->mainSchoolId;
            $item->pricePerKm = (new BikePrice)->getBetween($date)->amount;

            $repo->set($item);
            if ($distance == null) $this->setToast("Rit op datum {$rDate} verwijderd!");
            else $this->setToast("Rit '{$distance->alias} ({$distance->formatted->distance})' op datum {$rDate} opgeslagen!");
            $this->setReloadCalendar();
        }
    }

    private function postSettings()
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

    private function postExport()
    {
        $type = Helpers::input()->post('type')->getValue();
        $per = Helpers::input()->post('per')->getValue();
        $school = Helpers::input()->post('school');
        $start = Helpers::input()->post('start')->getValue();
        $end = Helpers::input()->post('end')->getValue();
        $exportAs = Helpers::input()->post('exportAs')->getValue();

        if (!is_null($school)) $school = $school->getValue();
        if (is_array($school) && !Strings::contains($school, ";")) {
            $s = [];
            foreach ($school as $sch)
                $s[] = $sch->getValue();

            $school = $s;
        } else if (Strings::contains($school, ";")) {
            $school = explode(";", $school);
        } else $school = [$school];

        if (Input::empty($school)) $this->setValidation("school", "Scholen moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($start) || Input::empty($start)) $this->setValidation("start", "Start datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
        if (!Input::check($end) || Input::empty($end)) $this->setValidation("end", "Eind datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            if (Strings::equal($per, "school") && Strings::equal($exportAs, 'xlsx')) $this->exportPerSchoolAsXlsx($school, $start, $end, $type);
            else if (Strings::equal($per, "school") && Strings::equal($exportAs, 'pdf')) $this->exportPerSchoolAsPdf($school, $start, $end, $type);
            else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "xlsx")) $this->exportPerTeacherAsXlsx($school, $start, $end, $type);
            else if (Strings::equal($per, "teacher") && Strings::equal($exportAs, "pdf")) $this->exportPerTeacherAsPdf($school, $start, $end, $type);
        }
    }

    // Delete Functions
    private function deleteDistance($id = null)
    {
        $id = explode(";", $id);
        $repo = new BikeDistance;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De afstand '{$item->alias}' is verwijderd!");
        }
    }

    // Export functions
    private function exportPerSchoolAsXlsx($schoolIds, $start, $end, $type)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per School - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ".xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        // $overview = [];
        $startRow = 6;
        $startColumn = "A";

        $excel = new Excel("{$folder}/{$filename}");
        $excel->setSheetTitle(0, "Overzicht");
        $excel->setCellValue(0, "A1:P1", "Fietsvergoeding - Overzicht per school - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk"), true, 14);
        $excel->setCellValue(0, "A2", "Startdatum");
        $excel->setCellValue(0, "B2", Clock::at($start)->format("d/m/Y"));
        $excel->setCellValue(0, "A3", "Einddatum");
        $excel->setCellValue(0, "B3", Clock::at($end)->format("d/m/Y"));
        $excel->setCellValue(0, "A4", "Laatste uitbetalingsdatum");
        $excel->setCellValue(0, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

        $overviewTotalDistance = $overviewTotalPrice = 0;
        $overviewRow = $startRow;
        $overviewColumn = $startColumn;

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "School", true, border: "b");
        $overviewColumn++;

        foreach ($monthsBetweenDates as $month) {
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $month, true, border: "b");
            $overviewColumn++;
        }

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "Totaal", true, border: "bl");
        $overviewColumn++;
        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "", true, border: "b");

        $overviewRow++;

        foreach ($schoolIds as $index => $schoolId) {
            $schoolTotalDistance = $schoolTotalPrice = 0;
            $schoolTotalDistancePerMonth = [];
            $groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end, $type);

            $school = $schoolRepo->get($schoolId)[0];
            $excel->createSheet($index + 1, $school->name);
            $excel->setCellValue($index + 1, "A1:P1", "Fietsvergoeding - {$school->name} - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk"), true, 14);
            $excel->setCellValue($index + 1, "A2", "Startdatum");
            $excel->setCellValue($index + 1, "B2", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A3", "Einddatum");
            $excel->setCellValue($index + 1, "B3", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index + 1, "A4", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index + 1, "B4", Clock::at($lastPayDate)->format("d/m/Y"));

            $schoolRow = $startRow;
            $schoolColumn = $startColumn;

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "Leerkracht", true, border: "b");
            $schoolColumn++;

            foreach ($monthsBetweenDates as $month) {
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $month, true, border: "b");
                $schoolColumn++;
            }

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "Totaal", true, border: "bl");
            $schoolColumn++;
            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "", true, border: "b");

            $schoolRow++;

            foreach ($groupedEvents as $user => $events) {
                $userTotalDistance = $userTotalPrice = 0;

                $schoolColumn = $startColumn;
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", $user);
                $schoolColumn++;

                foreach ($monthsBetweenDates as $month) {
                    $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format(($events[$month]['distance'] ?? 0), 2, ",", ".") . " km");
                    $userTotalDistance += $events[$month]['distance'] ?? 0;
                    $schoolTotalDistancePerMonth[$month] += $events[$month]['distance'] ?? 0;
                    $userTotalPrice += $events[$month]['price'] ?? 0;
                    $schoolColumn++;
                }

                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($userTotalDistance, 2, ",", ".") . " km", border: "l");
                $schoolColumn++;
                $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "€ " . number_format($userTotalPrice, 2, ",", "."));

                $schoolTotalDistance += $userTotalDistance;
                $schoolTotalPrice += $userTotalPrice;

                $schoolRow++;
            }

            $schoolColumn = $startColumn;
            $schoolColumn++;
            foreach ($monthsBetweenDates as $month) $schoolColumn++;

            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", number_format($schoolTotalDistance, 2, ",", ".") . " km", border: "t", borderStyle: Border::BORDER_DOUBLE);
            $schoolColumn++;
            $excel->setCellValue($index + 1, "{$schoolColumn}{$schoolRow}", "€ " . number_format($schoolTotalPrice, 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

            $overviewColumn = $startColumn;
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", $school->name, true, link: "sheet://'{$school->name}'!A1");
            $overviewColumn++;

            foreach ($monthsBetweenDates as $month) {
                $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($schoolTotalDistancePerMonth[$month] ?? 0), 2, ",", ".") . " km");
                $overviewColumn++;
            }

            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($schoolTotalDistance ?? 0), 2, ",", ".") . " km", border: "l");
            $overviewColumn++;
            $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "€ " .  number_format(($schoolTotalPrice ?? 0), 2, ",", "."));

            $overviewTotalDistance += $schoolTotalDistance;
            $overviewTotalPrice += $schoolTotalPrice;

            $overviewRow++;
        }

        $overviewColumn = $startColumn;
        $overviewColumn++;
        foreach ($monthsBetweenDates as $month) $overviewColumn++;

        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", number_format(($overviewTotalDistance ?? 0), 2, ",", ".") . " km", border: "t", borderStyle: Border::BORDER_DOUBLE);
        $overviewColumn++;
        $excel->setCellValue(0, "{$overviewColumn}{$overviewRow}", "€ " .  number_format(($overviewTotalPrice ?? 0), 2, ",", "."), border: "t", borderStyle: Border::BORDER_DOUBLE);

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    private function exportPerSchoolAsPdf($schoolIds, $start, $end, $type)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $schoolRepo = new School();
        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per School - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ".zip";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        foreach ($schoolIds as $index => $schoolId) {
            $schoolTotalDistance = $schoolTotalPrice = 0;
            $groupedEvents = $this->getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end, $type);

            $school = $schoolRepo->get($schoolId)[0];
            $pdf = new PDF($school->name, "{$folder}/{$school->name}.pdf", "L", "Fietsvergoeding - Overzicht - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ": {$school->name}");

            $pdf->AddPage();
            $pdf->Cell(60, 10, 'Startdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($start)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Einddatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($end)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Laatste uitbetalingsdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($lastPayDate)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');

            $table = [];
            $table['header'][] = [
                "title" => "Leerkracht",
                "border" => "B",
                "width" => 60
            ];

            foreach ($monthsBetweenDates as $month) {
                $table['header'][] = [
                    'title' => $month,
                    "border" => "B"
                ];
            }

            $table['header'][] = [
                "title" => "Totaal",
                "border" => "LB",
                "width" => 30
            ];

            $table['header'][] = [
                "border" => "B",
                "width" => 30
            ];

            foreach ($groupedEvents as $user => $events) {
                $userTotalDistance = $userTotalPrice = 0;

                $table['data'][$user][] = $user;

                foreach ($monthsBetweenDates as $month) {
                    $table['data'][$user][] = number_format(($events[$month]['distance'] ?? 0), 2, ",", ".") . " km";
                    $userTotalDistance += $events[$month]['distance'] ?? 0;
                    $userTotalPrice += $events[$month]['price'] ?? 0;
                }

                $table['data'][$user][] = [
                    "text" => number_format($userTotalDistance, 2, ",", ".") . " km",
                    "border" => "L"
                ];
                $table['data'][$user][] = "€ " . number_format($userTotalPrice, 2, ",", ".");

                $schoolTotalDistance += $userTotalDistance;
                $schoolTotalPrice += $userTotalPrice;
            }

            $table['data']['total'][] = [];
            foreach ($monthsBetweenDates as $m) {
                $table['data']['total'][] = [];
            }

            $table['data']['total'][] = [
                "text" => number_format($schoolTotalDistance, 2, ",", ".") . " km",
                "border" => "T"
            ];
            $table['data']['total'][] = [
                "text" => "€ " . number_format($schoolTotalPrice, 2, ",", "."),
                "border" => "T"
            ];

            $pdf->Ln(10);
            $pdf->table($table);

            $pdf->save();
        }

        $zipFile = new ZIP("{$folder}/{$filename}");
        $zipFile->addDir($folder);
        $zipFile->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    private function exportPerTeacherAsXlsx($schoolIds, $start, $end, $type)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per Leerkracht - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ".xlsx";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");

        $excel = new Excel("{$folder}/{$filename}");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds, $type);

        $startRow = 9;
        $startColumn = "A";

        foreach ($groupedEvents as $username => $userEvent) {
            $userRow = $startRow;
            $userColumn = $startColumn;

            $userTotalSingle = $userTotalDouble = $userTotalPrice = 0;

            $index = array_search($username, array_keys($groupedEvents));
            $user = $userEvent['user'];
            $address = $userEvent['address'];
            $events = $userEvent['events'];

            if ($index == 0) $excel->setSheetTitle($index, $user->formatted->fullNameReversed);
            else $excel->createSheet($index, $user->formatted->fullNameReversed);

            $excel->setCellValue($index, "A1:E1", "Fietsvergoeding - Overzicht - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ": {$user->formatted->fullNameReversed}", true, 14);
            $excel->setCellValue($index, "A2", "Hoofdschool");
            $excel->setCellValue($index, "B2:E2", $user->linked->mainSchool->name);
            $excel->setCellValue($index, "A3", "Huidig adres");
            $excel->setCellValue($index, "B3:E3", $address->formatted->address);
            $excel->setCellValue($index, "A4", "Rekeningnummer");
            $excel->setCellValue($index, "B4:E4", $user->bankAccount);
            $excel->setCellValue($index, "A5", "Startdatum");
            $excel->setCellValue($index, "B5:E5", Clock::at($start)->format("d/m/Y"));
            $excel->setCellValue($index, "A6", "Einddatum");
            $excel->setCellValue($index, "B6:E6", Clock::at($end)->format("d/m/Y"));
            $excel->setCellValue($index, "A7", "Laatste uitbetalingsdatum");
            $excel->setCellValue($index, "B7:E7", Clock::at($lastPayDate)->format("d/m/Y"));

            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Datum", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Afstand - Enkel", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Afstand - Dubbel", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Vergoeding/km", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Vergoeding totaal", true);
            $userRow++;

            foreach ($monthsBetweenDates as $month) {
                $monthTotalSingle = $monthTotalDouble = $monthTotalPrice = 0;

                $monthColumn = $startColumn;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}:E{$userRow}", $month, true);
                $userRow++;

                foreach ($events[$month] as $event) {
                    $eventColumn = $startColumn;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", Clock::at($event->date)->format("d/m/Y"));
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", number_format($event->distance, 2, ",", ".") . " km");
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", number_format($event->distance * 2, 2, ",", ".") . " km");
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", "€ " . number_format($event->pricePerKm, 2, ",", "."));
                    $eventColumn++;
                    $excel->setCellValue($index, "{$eventColumn}{$userRow}", "€ " . number_format($event->pricePerKm * ($event->distance * 2), 2, ",", "."));

                    $userRow++;

                    $monthTotalSingle += $event->distance;
                    $monthTotalDouble += $event->distance * 2;
                    $monthTotalPrice += $event->pricePerKm * ($event->distance * 2);
                }

                $monthColumn = $startColumn;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", count($events[$month] ?? []) . " rit(ten)", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", number_format($monthTotalSingle, 2, ",", ".") . " km", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", number_format($monthTotalDouble, 2, ",", ".") . " km", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", "", border: 't', borderStyle: Border::BORDER_DOUBLE);
                $monthColumn++;
                $excel->setCellValue($index, "{$monthColumn}{$userRow}", "€ " . number_format($monthTotalPrice, 2, ",", "."), border: 't', borderStyle: Border::BORDER_DOUBLE);

                $userRow++;
                $userRow++;

                $userTotalSingle += $monthTotalSingle;
                $userTotalDouble += $monthTotalDouble;
                $userTotalPrice += $monthTotalPrice;
            }

            $userColumn = $startColumn;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "Totaal", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", number_format($userTotalSingle, 2, ",", ".") . " km", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", number_format($userTotalDouble, 2, ",", ".") . " km", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "", true);
            $userColumn++;
            $excel->setCellValue($index, "{$userColumn}{$userRow}", "€ " . number_format($userTotalPrice, 2, ",", "."), true);
        }

        $excel->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    private function exportPerTeacherAsPdf($schoolIds, $start, $end, $type)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $lastPayDate = $settings["lastPayDate"];

        $folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
        $filename = "Fietsvergoeding - Export Per Leerkracht - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ".zip";
        $monthsBetweenDates = Date::monthsBetweenDates($start, $end, "F Y");
        $groupedEvents = $this->getEventsGroupedByMonthByTeacherBySchool($start, $end, $schoolIds, $type);

        foreach ($groupedEvents as $username => $userEvent) {
            $index = array_search($username, array_keys($groupedEvents));
            $user = $userEvent['user'];
            $address = $userEvent['address'];
            $events = $userEvent['events'];

            $userTotalSingle = $userTotalDouble = $userTotalPrice = 0;
            $pdf = new PDF($user->fullNameReversed, "{$folder}/{$user->formatted->fullNameReversed}.pdf", "P", "Fietsvergoeding - Overzicht - " . (Strings::equal($type, "HW") ? "Woon-Werk" : "Werk-Werk") . ": {$user->fullNameReversed}");

            $pdf->AddPage();
            $pdf->Cell(60, 10, 'Hoofdschool', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $user->linked->mainSchool->name, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Huidig adres', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $address->formatted->address, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Rekeningnummer', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, $user->bankAccount, ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Startdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($start)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Einddatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($end)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(60, 10, 'Laatste uitbetalingsdatum', ln: 0, align: 'L', calign: 'C', valign: 'C');
            $pdf->Cell(0, 10, Clock::at($lastPayDate)->format("d/m/Y"), ln: 1, align: 'L', calign: 'C', valign: 'C');

            $table = [];
            $table['header'][] = ["title" => "Datum"];
            $table['header'][] = ["title" => "Afstand - Enkel"];
            $table['header'][] = ["title" => "Afstand - Dubbel"];
            $table['header'][] = ["title" => "Vergoeding/km"];
            $table['header'][] = ["title" => "Vergoeding totaal"];

            foreach ($monthsBetweenDates as $month) {
                $monthTotalSingle = $monthTotalDouble = $monthTotalPrice = 0;

                $table['data'][][] = [
                    "text" => $month,
                    "width" => 0
                ];

                foreach ($events[$month] as $event) {
                    $table['data'][$event->date][] = Clock::at($event->date)->format("d/m/Y");
                    $table['data'][$event->date][] = number_format($event->distance, 2, ",", ".") . " km";
                    $table['data'][$event->date][] = number_format($event->distance * 2, 2, ",", ".") . " km";
                    $table['data'][$event->date][] = "€ " . number_format($event->pricePerKm, 2, ",", ".");
                    $table['data'][$event->date][] = "€ " . number_format($event->pricePerKm * ($event->distance * 2), 2, ",", ".");

                    $monthTotalSingle += $event->distance;
                    $monthTotalDouble += $event->distance * 2;
                    $monthTotalPrice += $event->pricePerKm * ($event->distance * 2);
                }

                $table['data'][$month][] =
                    [
                        "text" => count($events[$month] ?? []) . " rit(ten)",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => number_format($monthTotalSingle, 2, ",", ".") . " km",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => number_format($monthTotalDouble, 2, ",", ".") . " km",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => "",
                        "border" => 'T'
                    ];

                $table['data'][$month][] =
                    [
                        "text" => "€ " . number_format($monthTotalPrice, 2, ",", "."),
                        "border" => 'T'
                    ];

                $table['data'][][] = "";

                $userTotalSingle += $monthTotalSingle;
                $userTotalDouble += $monthTotalDouble;
                $userTotalPrice += $monthTotalPrice;
            }

            $table2 = [];
            $table2['header'][] = ["title" => "Totaal"];
            $table2['header'][] = ["title" => number_format($userTotalSingle, 2, ",", ".") . " km"];
            $table2['header'][] = ["title" => number_format($userTotalDouble, 2, ",", ".") . " km"];
            $table2['header'][] = ["title" => ""];
            $table2['header'][] = ["title" => "€ " . number_format($userTotalPrice, 2, ",", ".")];

            $pdf->Ln(10);
            $pdf->table($table);

            $pdf->Ln(10);
            $pdf->table($table2);

            $pdf->save();
        }

        $zipFile = new ZIP("{$folder}/{$filename}");
        $zipFile->addDir($folder);
        $zipFile->save();
        if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
    }

    // Other functions
    private function getEventsGroupedByTeacherAndByMonthBySchoolId($schoolId, $start, $end, $type)
    {
        $eventRepo = new BikeEvent;
        $userRepo = new RepositoryUser();
        $eventsGrouped = [];

        $events = $eventRepo->getByUserMainSchoolIdAndType($schoolId, $type);
        $events = Arrays::filter($events, fn($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));

        Arrays::each($events, fn($e) => $e->user = Arrays::first($userRepo->get($e->userId))->formatted->fullNameReversed);
        $events = Arrays::orderBy($events, "user");

        foreach ($events as $event) {
            if (Strings::equal($event->distance, 0)) continue;
            $eventsGrouped[$event->user][Clock::at($event->date)->format("F Y")]['distance'] += floatval($event->distance) * 2;
            $eventsGrouped[$event->user][Clock::at($event->date)->format("F Y")]['price'] += floatval($event->pricePerKm) * (floatval($event->distance) * 2);
        }

        return $eventsGrouped;
    }

    private function getEventsGroupedByMonthByTeacherBySchool($start, $end, $allowedSchoolIds, $type)
    {
        $eventRepo = new BikeEvent;
        $userRepo = new RepositoryUser;
        $userAddressRepo = new UserAddress;
        $eventsGrouped = [];

        $users = Arrays::orderBy($userRepo->get(), "name");

        foreach ($users as $user) {
            if (Strings::isBlank($user->username)) continue;
            $eventsGrouped[$user->username]['user'] = $user;

            if (!Arrays::contains($allowedSchoolIds, $user->mainSchoolId)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $address = $userAddressRepo->getCurrentByUserId($user->id);
            $eventsGrouped[$user->username]['address'] = $address;

            $events = $eventRepo->getByUserIdAndType($user->id, $type);
            if (!count($events)) {
                unset($eventsGrouped[$user->username]);
                continue;
            }

            $events = Arrays::filter($events, fn($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));
            $events = Arrays::orderBy($events, "date");

            foreach ($events as $event) {
                if (Strings::equal($event->distance, 0)) continue;
                $eventsGrouped[$user->username]['events'][Clock::at($event->date)->format("F Y")][] = $event;
            }
        }

        return $eventsGrouped;
    }
}
