<?php

set_time_limit(0);

use Core\Config;
use Helpers\Date;
use Helpers\Strings as HelpersStrings;
use Security\Request;
use O365\Objects\User;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\BikeEvent;
use Database\Repository\BikeProfile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpZip\ZipFile;

require_once __DIR__ . "/../../../app/autoload.php";

$bikeEventRepo = new BikeEvent;
$bikeProfileRepo = new BikeProfile;

$return = [];
$now = date("U");
$start = Clock::at($_POST['perTeacherStart']);
$end = Clock::at($_POST['perTeacherEnd']);
$aspdf = isset($_POST['teacherAsPdf']);
$monthsBetweenDates = Date::monthsBetweenDates($start, $end);

$schools = Config::get("schools");
$prices = Config::get("tool/bike/price");
$months = Config::get("months/long");

if (!file_exists(LOCATION_PUBLIC_DOWNLOADS)) mkdir(LOCATION_PUBLIC_DOWNLOADS, 0777);
if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike", 0777);
if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}", 0777);

$bikeEvents = $bikeEventRepo->get();
$bikeEvents = Arrays::filter($bikeEvents, fn ($be) => Clock::at($be->date)->isAfterOrEqualTo($start) && Clock::at($be->date)->isBeforeOrEqualTo($end));
$teachersUpn = array_values(array_unique(Arrays::map($bikeEvents, fn ($be) => $be->upn)));
$profiles = [];

foreach ($teachersUpn as $upn) $profiles[] = $bikeProfileRepo->getByUpn($upn);
Arrays::orderBy($profiles, 'upn');

// Calculations
$array = [];

foreach ($schools as $schoolIndex => $schoolName) {
    $array[$schoolIndex]['school'] = $schoolName;

    foreach ($profiles as $profile) {
        if (!Strings::contains($profile->upn, '@')) continue;
        if (!Strings::equal($profile->mainSchool, $schoolIndex)) continue;

        $teacher = (new User)->get($profile->upn);
        $array[$schoolIndex]['perProfile'][$profile->upn]['user'] = $teacher;
        $array[$schoolIndex]['perProfile'][$profile->upn]['profile'] = $profile;

        foreach ($monthsBetweenDates as $monthFormat => $month) {
            $teacherBikeEvents = Arrays::filter($bikeEvents, fn ($be) => Strings::equalsIgnoreCase($be->upn, $profile->upn) && Strings::equalsIgnoreCase(Clock::at($be->date)->format("m/Y"), $monthFormat) && $be->distance > 0);
            $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month] = $teacherBikeEvents;
        }
    }
}

// Printing
$columnStart = "A";
$rowStart = 8;
$spreadsheet = new Spreadsheet;

foreach ($array as $i => $school) {
    foreach ($school['perProfile'] as $j => $perProfile) {
        $profileKm = $profileDoubleKm = $profileCosts = 0;
        $row = $rowStart;

        $sheet = $spreadsheet->createSheet()->setTitle(Strings::abbreviate($perProfile['user']->getDisplayName(), 30, true));
        $sheet
            ->mergeCells("A1:E1")
            ->setCellValue("A1", "Fietsvergoeding - Overzicht: {$perProfile['user']->getDisplayName()}")
            ->getStyle("A1")
            ->getFont()
            ->setBold(true)
            ->setSize(14);

        $sheet
            ->setCellValue("A2", "School: ")
            ->mergeCells("B2:E2")
            ->setCellValue("B2", $schools[$perProfile['profile']->mainSchool])
            ->setCellValue("A3", "Adres: ")
            ->mergeCells("B3:E3")
            ->setCellValue("B3", $perProfile['profile']->address_street . " " . $perProfile['profile']->address_number . ($perProfile['profile']->address_bus ? " bus " . $perProfile['profile']->address_bus : '') . ", " . $perProfile['profile']->address_zipcode . " " . $perProfile['profile']->address_city)
            ->setCellValue("A4", "Rekeningnummer: ")
            ->mergeCells("B4:E4")
            ->setCellValue("B4", $perProfile['profile']->address_country . $perProfile['profile']->bankAccount)
            ->setCellValue("A5", "Startdatum: ")
            ->mergeCells("B5:E5")
            ->setCellValue("B5", $start->format("d/m/Y"))
            ->setCellValue("A6", "Einddatum: ")
            ->mergeCells("B6:E6")
            ->setCellValue("B6", $end->format("d/m/Y"));

        $sheet->setCellValue("A{$row}", "Datum")->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("B{$row}", "Afstand - enkel")->getStyle("B{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("C{$row}", "Afstand - dubbel")->getStyle("C{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("D{$row}", "Vergoeding/km")->getStyle("D{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("E{$row}", "Vergoeding")->getStyle("E{$row}")->getFont()->setBold(true);

        $row += 2;

        foreach ($perProfile['perMonth'] as $k => $perMonth) {
            $perMonthKm = $perMonthDoubleKm = $perMonthCosts = 0;

            $sheet
                ->mergeCells("A{$row}:E{$row}")
                ->setCellValue("A{$row}", $k)
                ->getStyle("A{$row}")
                ->getFont()
                ->setBold(true);

            $row++;

            foreach ($perMonth as $bikeEvent) {
                $bikeEventKm = $bikeEvent->distanceInKm;
                $bikeEventDoubleKm = $bikeEventKm * 2;
                $bikeEventPricePerKm = Arrays::first(Arrays::filter($prices, fn ($p) => Clock::at($bikeEvent->date)->isAfterOrEqualTo(Clock::at($p['start'])) && Clock::at($bikeEvent->date)->isBeforeOrEqualTo(Clock::at($p['end']))))['amount'];
                $bikeEventCosts = $bikeEventDoubleKm * $bikeEventPricePerKm;

                $perMonthKm += $bikeEventKm;
                $perMonthDoubleKm += $bikeEventDoubleKm;
                $perMonthCosts += $bikeEventCosts;

                $sheet
                    ->setCellValue("A{$row}", Clock::at($bikeEvent->date)->format("d/m/Y"))
                    ->setCellValue("B{$row}", number_format(round($bikeEventKm, 2), 2, ",", ".") . " km")
                    ->setCellValue("C{$row}", number_format(round($bikeEventDoubleKm, 2), 2, ",", ".") . " km")
                    ->setCellValue("D{$row}", "€ " . number_format(round($bikeEventPricePerKm, 2), 2, ",", "."))
                    ->setCellValue("E{$row}", "€ " . number_format(round($bikeEventCosts, 2), 2, ",", "."));

                $row++;
            }

            $profileKm += $perMonthKm;
            $profileDoubleKm += $perMonthDoubleKm;
            $profileCosts += $perMonthCosts;

            $sheet
                ->setCellValue("B{$row}", number_format(round($perMonthKm, 2), 2, ",", ".") . " km")
                ->getStyle("B{$row}")
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_DOUBLE);

            $sheet
                ->setCellValue("C{$row}", number_format(round($perMonthDoubleKm, 2), 2, ",", ".") . " km")
                ->getStyle("C{$row}")
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_DOUBLE);

            $sheet
                ->getStyle("D{$row}")
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_DOUBLE);

            $sheet
                ->setCellValue("E{$row}", "€ " . number_format(round($perMonthCosts, 2), 2, ",", "."))
                ->getStyle("E{$row}")
                ->getBorders()
                ->getTop()
                ->setBorderStyle(Border::BORDER_DOUBLE);

            $row += 2;
        }

        $sheet
            ->setCellValue("A{$row}", "Totaal")
            ->setCellValue("B{$row}", number_format(round($profileKm, 2), 2, ",", ".") . " km")
            ->setCellValue("C{$row}", number_format(round($profileDoubleKm, 2), 2, ",", ".") . " km")
            ->setCellValue("E{$row}", "€ " . number_format(round($profileCosts, 2), 2, ",", "."))
            ->getStyle("A{$row}:E{$row}")
            ->getFont()
            ->setBold(true);
    }
}

$spreadsheet->removeSheetByIndex(0);

for ($i = 0; $i < $spreadsheet->getSheetCount(); $i++) {
    foreach ($spreadsheet->getSheet($i)->getColumnIterator() as $c) {
        $spreadsheet->getSheet($i)
            ->setSelectedCell("A1")
            ->getColumnDimension($c->getColumnIndex())
            ->setAutoSize(true);
    }

    $spreadsheet
        ->getSheet($i)
        ->getSheetView()
        ->setZoomScale(100);

    $spreadsheet
        ->getSheet($i)
        ->getPageSetup()
        ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
        ->setPaperSize(PageSetup::PAPERSIZE_A4)
        ->setFitToWidth(1)
        ->setFitToHeight(0);
}

if ($aspdf) {
    try {
        if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/pdf")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/pdf", 0777);
        if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/zip")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/zip", 0777);

        $zipFile = new ZipFile;

        for ($i = 0; $i < $spreadsheet->getSheetCount(); $i++) {
            $sheetTitle = $spreadsheet->getSheet($i)->getTitle();
            $sheetTitleReplaced = HelpersStrings::replaceSpecialCharacters($sheetTitle);

            $writer = new Mpdf($spreadsheet);
            $writer->setSheetIndex($i);
            $writer->save(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/pdf/{$sheetTitleReplaced}.pdf");
            $zipFile->addFile(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/pdf/{$sheetTitleReplaced}.pdf", "{$sheetTitleReplaced}.pdf");
        }

        $zipFile->saveAsFile(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/zip/exportPerTeacher.zip");
        $zipFile->close();
        $return['download'] = Request::host(protocol: 'https') . "/public/downloads/bike/{$now}/zip/exportPerTeacher.zip";
    } catch (\Exception $e) {
        die(var_dump($e->getMessage()));
    }
} else {
    if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel", 0777);

    $writer = new Xlsx($spreadsheet);
    $writer->save(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel/exportPerTeacher.xlsx");

    $return['download'] = Request::host(protocol: 'https') . "/public/downloads/bike/{$now}/excel/exportPerTeacher.xlsx";
}

echo Json::safeEncode($return);
