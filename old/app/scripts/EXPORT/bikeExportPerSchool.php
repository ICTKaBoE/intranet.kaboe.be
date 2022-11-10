<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

use Core\Config;
use Helpers\Date;
use Helpers\Strings as HelpersStrings;
use PhpZip\ZipFile;
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
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

require_once __DIR__ . "/../../../app/autoload.php";

$bikeEventRepo = new BikeEvent;
$bikeProfileRepo = new BikeProfile;

$return = [];
$start = Clock::at($_POST['perSchoolStart']);
$end = Clock::at($_POST['perSchoolEnd']);
$aspdf = isset($_POST['schoolAsPdf']);
$monthsBetweenDates = Date::monthsBetweenDates($start, $end, false);

$schools = Config::get("schools");
$prices = Config::get("tool/bike/price");
$months = Config::get("months/short");

if (!file_exists(LOCATION_PUBLIC_DOWNLOADS)) mkdir(LOCATION_PUBLIC_DOWNLOADS, 0777);
if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike", 0777);

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
    $array[$schoolIndex]['total']['kilometers'] =
        $array[$schoolIndex]['total']['costs'] = 0;

    foreach ($monthsBetweenDates as $monthFormat => $month) {
        $array[$schoolIndex]['perMonth'][$month]['kilometers'] =
            $array[$schoolIndex]['perMonth'][$month]['costs'] = 0;

        foreach ($profiles as $profile) {
            if (!Strings::contains($profile->upn, '@')) continue;
            if (!Strings::equal($profile->mainSchool, $schoolIndex)) continue;

            $teacher = (new User)->get($profile->upn);
            $array[$schoolIndex]['perProfile'][$profile->upn]['user'] = $teacher;
            $array[$schoolIndex]['perProfile'][$profile->upn]['profile'] = $profile;

            $teacherBikeEvents = Arrays::filter($bikeEvents, fn ($be) => Strings::equalsIgnoreCase($be->upn, $profile->upn) && Strings::equalsIgnoreCase(Clock::at($be->date)->format("m/Y"), $monthFormat) && $be->distance > 0);

            foreach ($teacherBikeEvents as $bikeEvent) {
                $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['kilometers'] += $bikeEvent->distanceInKm * 2;
                $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['costs'] += ($bikeEvent->distanceInKm * 2) * Arrays::first(Arrays::filter($prices, fn ($p) => Clock::at($bikeEvent->date)->isAfterOrEqualTo(Clock::at($p['start'])) && Clock::at($bikeEvent->date)->isBeforeOrEqualTo(Clock::at($p['end']))))['amount'];

                $array[$schoolIndex]['perProfile'][$profile->upn]['total']['kilometers'] += $bikeEvent->distanceInKm * 2;
                $array[$schoolIndex]['perProfile'][$profile->upn]['total']['costs'] += ($bikeEvent->distanceInKm * 2) * Arrays::first(Arrays::filter($prices, fn ($p) => Clock::at($bikeEvent->date)->isAfterOrEqualTo(Clock::at($p['start'])) && Clock::at($bikeEvent->date)->isBeforeOrEqualTo(Clock::at($p['end']))))['amount'];
            }

            $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['kilometers'] = round($array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['kilometers'], 2);
            $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['costs'] = round($array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['costs'], 2);

            $array[$schoolIndex]['perMonth'][$month]['kilometers'] += $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['kilometers'];
            $array[$schoolIndex]['perMonth'][$month]['costs'] += $array[$schoolIndex]['perProfile'][$profile->upn]['perMonth'][$month]['costs'];

            $array[$schoolIndex]['perProfile'][$profile->upn]['total']['kilometers'] = round($array[$schoolIndex]['perProfile'][$profile->upn]['total']['kilometers'], 2);
            $array[$schoolIndex]['perProfile'][$profile->upn]['total']['costs'] = round($array[$schoolIndex]['perProfile'][$profile->upn]['total']['costs'], 2);
        }

        $array[$schoolIndex]['perMonth'][$month]['kilometers'] = round($array[$schoolIndex]['perMonth'][$month]['kilometers'], 2);
        $array[$schoolIndex]['perMonth'][$month]['costs'] = round($array[$schoolIndex]['perMonth'][$month]['costs'], 2);

        $array[$schoolIndex]['total']['kilometers'] += $array[$schoolIndex]['perMonth'][$month]['kilometers'];
        $array[$schoolIndex]['total']['costs'] += $array[$schoolIndex]['perMonth'][$month]['costs'];
    }

    $array[$schoolIndex]['total']['kilometers'] = round($array[$schoolIndex]['total']['kilometers'], 2);
    $array[$schoolIndex]['total']['costs'] = round($array[$schoolIndex]['total']['costs'], 2);
}

// Printing
$monthColumnStart = "B";
$rowStart = 6;
$spreadsheet = new Spreadsheet;

// Overview
$overviewSheet = $spreadsheet
    ->getActiveSheet()
    ->setTitle("Overzicht");

$overviewSheet
    ->mergeCells("A1:P1")
    ->setCellValue("A1", "Fietsvergoeding - Overzicht per school")
    ->getStyle("A1")
    ->getFont()
    ->setBold(true)
    ->setSize(14);

$overviewSheet
    ->setCellValue("A2", "Startdatum: ")
    ->setCellValue("B2", $start->format("d/m/Y"))
    ->setCellValue("A3", "Einddatum: ")
    ->setCellValue("B3", $end->format("d/m/Y"))
    ->setCellValue("A4", "Laatste uitbetalingsdatum: ")
    ->setCellValue("B4", Config::get("tool/bike/lastPayDate") == false ? 'N.V.T.' : Clock::at(Config::get("tool/bike/lastPayDate"))->format("d/m/Y"));

$overviewRow = $rowStart;
$overviewSheet
    ->setCellValue("A{$overviewRow}", "School")
    ->getStyle("A{$overviewRow}")
    ->getFont()
    ->setBold(true);

$overviewSheet
    ->getStyle("A{$overviewRow}")
    ->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THIN);

$overviewColumn = $monthColumnStart;
foreach ($monthsBetweenDates as $index => $month) {
    $overviewSheet
        ->setCellValue("{$overviewColumn}{$overviewRow}", $month)
        ->getStyle("{$overviewColumn}{$overviewRow}")
        ->getFont()
        ->setBold(true);

    $overviewSheet
        ->getStyle("{$overviewColumn}{$overviewRow}")
        ->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN);

    $overviewColumn++;
}

$overviewSheet
    ->setCellValue("{$overviewColumn}{$overviewRow}", " Totaal")
    ->getStyle("{$overviewColumn}{$overviewRow}")
    ->getFont()
    ->setBold(true);

$overviewSheet
    ->getStyle("{$overviewColumn}{$overviewRow}")
    ->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THIN);

$overviewSheet
    ->getStyle("{$overviewColumn}{$overviewRow}")
    ->getBorders()
    ->getLeft()
    ->setBorderStyle(Border::BORDER_THIN);

$overviewColumn++;

$overviewSheet
    ->getStyle("{$overviewColumn}{$overviewRow}")
    ->getBorders()
    ->getBottom()
    ->setBorderStyle(Border::BORDER_THIN);

$overviewRow++;
foreach ($array as $i => $school) {
    $overviewSheet
        ->setCellValue("A{$overviewRow}", $school['school'])
        ->getCell("A{$overviewRow}")
        ->getHyperlink()
        ->setUrl("sheet://'{$school['school']}'!A1");

    $overviewColumn = $monthColumnStart;
    foreach ($school['perMonth'] as $monthName => $perMonth) {
        $column = $monthColumnStart;
        foreach ($monthsBetweenDates as $m) {
            if ($m == $monthName) break;
            $column++;
        }

        $overviewSheet->setCellValue("{$column}{$overviewRow}", "{$perMonth['kilometers']} km     ");

        $overviewColumn = $column;
    }

    $overviewColumn++;
    $overviewLastColumn = $overviewColumn;

    $overviewSheet
        ->getStyle("{$overviewColumn}{$overviewRow}")
        ->getBorders()
        ->getLeft()
        ->setBorderStyle(Border::BORDER_THIN);

    $overviewSheet->setCellValue("{$overviewColumn}{$overviewRow}", " " . number_format(round($school['total']['kilometers'], 2), 2, ",", ".") . " km ");

    $overviewColumn++;
    $overviewSheet->setCellValue("{$overviewColumn}{$overviewRow}", "€ " . number_format(round($school['total']['costs'], 2), 2, ",", "."));

    // School
    $schoolRow = $rowStart;
    $schoolSheet = $spreadsheet->createSheet()->setTitle($school['school']);

    $schoolSheet
        ->mergeCells("A1:P1")
        ->setCellValue("A1", "Fietsvergoeding - {$school['school']}")
        ->getStyle("A1")
        ->getFont()
        ->setBold(true)
        ->setSize(14);

    $schoolSheet
        ->setCellValue("A2", "Startdatum: ")
        ->setCellValue("B2", $start->format("d/m/Y"))
        ->setCellValue("A3", "Einddatum: ")
        ->setCellValue("B3", $end->format("d/m/Y"))
        ->setCellValue("A4", "Laatste uitbetalingsdatum: ")
        ->setCellValue("B4", Config::get("tool/bike/lastPayDate") == false ? 'N.V.T.' : Clock::at(Config::get("tool/bike/lastPayDate"))->format("d/m/Y"));

    $schoolSheet
        ->setCellValue("A{$schoolRow}", "Leerkracht")
        ->getStyle("A{$schoolRow}")
        ->getFont()
        ->setBold(true);

    $schoolSheet
        ->getStyle("A{$schoolRow}")
        ->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN);

    $schoolColumn = $monthColumnStart;
    foreach ($monthsBetweenDates as $index => $month) {
        $schoolSheet
            ->setCellValue("{$schoolColumn}{$schoolRow}", $month)
            ->getStyle("{$schoolColumn}{$schoolRow}")
            ->getFont()
            ->setBold(true);

        $schoolSheet
            ->getStyle("{$schoolColumn}{$schoolRow}")
            ->getBorders()
            ->getBottom()
            ->setBorderStyle(Border::BORDER_THIN);

        $schoolColumn++;
    }

    $schoolSheet
        ->setCellValue("{$schoolColumn}{$schoolRow}", " Totaal")
        ->getStyle("{$schoolColumn}{$schoolRow}")
        ->getFont()
        ->setBold(true);

    $schoolSheet
        ->getStyle("{$schoolColumn}{$schoolRow}")
        ->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN);

    $schoolSheet
        ->getStyle("{$schoolColumn}{$schoolRow}")
        ->getBorders()
        ->getLeft()
        ->setBorderStyle(Border::BORDER_THIN);

    $schoolColumn++;

    $schoolSheet
        ->getStyle("{$schoolColumn}{$schoolRow}")
        ->getBorders()
        ->getBottom()
        ->setBorderStyle(Border::BORDER_THIN);

    $schoolRow++;
    foreach ($school['perProfile'] as $upn => $perProfile) {
        $schoolSheet
            ->setCellValue("A{$schoolRow}", $perProfile['user']->getDisplayName());

        $schoolColumn = $monthColumnStart;
        foreach ($perProfile['perMonth'] as $monthName => $perProfileMonth) {
            $column = $monthColumnStart;
            foreach ($monthsBetweenDates as $m) {
                if ($m == $monthName) break;
                $column++;
            }

            $schoolSheet->setCellValue("{$column}{$schoolRow}", number_format(round($perProfileMonth['kilometers'], 2), 2, ",", ".") . " km     ");

            $schoolColumn = $column;
        }

        $schoolColumn++;
        $schoolLastColumn = $schoolColumn;

        $schoolSheet
            ->getStyle("{$schoolColumn}{$schoolRow}")
            ->getBorders()
            ->getLeft()
            ->setBorderStyle(Border::BORDER_THIN);

        $schoolSheet->setCellValue("{$schoolColumn}{$schoolRow}", " " . number_format(round($perProfile['total']['kilometers'], 2), 2, ",", ".") . " km ");

        $schoolColumn++;
        $schoolSheet->setCellValue("{$schoolColumn}{$schoolRow}", "€ " . number_format(round($perProfile['total']['costs'], 2), 2, ",", "."));

        $schoolRow++;
    }

    $schoolSheet
        ->getStyle("{$schoolLastColumn}{$schoolRow}:{$schoolColumn}{$schoolRow}")
        ->getBorders()
        ->getTop()
        ->setBorderStyle(Border::BORDER_DOUBLE);

    $lastSchoolRow = $schoolRow - 1;
    $schoolSheet->setCellValue("{$schoolLastColumn}{$schoolRow}", " " . number_format(round($school['total']['kilometers'], 2), 2, ",", ".") . " km ");
    $schoolLastColumn++;
    $schoolSheet->setCellValue("{$schoolLastColumn}{$schoolRow}", "€ " . number_format(round($school['total']['costs'], 2), 2, ",", "."));

    $overviewRow++;
}

for ($i = 0; $i < $spreadsheet->getSheetCount(); $i++) {
    foreach ($spreadsheet->getSheet($i)->getColumnIterator() as $c) {
        $spreadsheet->getSheet($i)
            ->setSelectedCell("A1")
            ->getColumnDimension($c->getColumnIndex())
            ->setAutoSize(true);
    }

    $spreadsheet
        ->getSheet($i)
        ->setSelectedCell("A1")
        ->getSheetView()
        ->setZoomScale(100);

    $spreadsheet
        ->getSheet($i)
        ->getPageSetup()
        ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
        ->setPaperSize(PageSetup::PAPERSIZE_A4)
        ->setFitToWidth(1)
        ->setFitToHeight(0);
}

if ($aspdf) {
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

    $zipFile->saveAsFile(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/zip/exportPerSchool.zip");
    $return['download'] = Request::host(protocol: 'https') . "/public/downloads/bike/{$now}/zip/exportPerSchool.zip";
} else {
    if (!file_exists(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel")) mkdir(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel", 0777);

    $writer = new Xlsx($spreadsheet);
    $writer->save(LOCATION_PUBLIC_DOWNLOADS . "bike/{$now}/excel/exportPerSchool.xlsx");

    $return['download'] = Request::host(protocol: 'https') . "/public/downloads/bike/{$now}/excel/exportPerSchool.xlsx";
}

echo Json::safeEncode($return);
