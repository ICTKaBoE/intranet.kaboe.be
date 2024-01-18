<?php

namespace Controllers\API;

use Helpers\PDF;
use Helpers\ZIP;
use Helpers\Date;
use Helpers\Excel;
use Router\Helpers;
use Security\Input;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Log;
use Controllers\ApiController;
use Database\Object\ModuleSetting as ObjectModuleSetting;
use Database\Repository\Module;
use Database\Repository\School;
use Database\Repository\ModuleSetting;
use Database\Repository\TemperatureRegistration;

class TemperatureRegistrationController extends ApiController
{
	//GET
	public function getDashboard($view, $id = null)
	{
		if ($view == "table") {
			$schoolId = Helpers::url()->getParam("schoolId");
			$start = Helpers::url()->getParam("start");
			$end = Helpers::url()->getParam("end");


			$this->appendToJson(
				'columns',
				[
					[
						"type" => "badge",
						"title" => "School",
						"data" => "school.name",
						"backgroundColorCustom" => "school.color",
						"width" => 120
					],
					[
						"title" => "Dag",
						"data" => "day",
						"width" => 100
					],
					[
						"title" => "Datum",
						"data" => "date",
						"width" => 150
					],
					[
						"title" => "Uur",
						"data" => "time",
						"width" => 100
					],
					[
						"title" => "Naam",
						"data" => "personWithInitials",
						"width" => 300
					],
					[
						"type" => "badge",
						"title" => "Soep (°C)",
						"data" => "soupTemp",
						"backgroundColorCustom" => "soupTempColor",
						"width" => 100
					],
					[
						"type" => "badge",
						"title" => "Aardappel/Pasta/Rijst (°C)",
						"data" => "potatoRicePastaTemp",
						"backgroundColorCustom" => "potatoRicePastaTempColor",
						"width" => 100
					],
					[
						"type" => "badge",
						"title" => "Groente (°C)",
						"data" => "vegetablesTemp",
						"backgroundColorCustom" => "vegetablesTempColor",
						"width" => 100
					],
					[
						"type" => "badge",
						"title" => "Vlees/vis (°C)",
						"data" => "meatFishTemp",
						"backgroundColorCustom" => "meatFishTempColor",
						"width" => 100
					],
					[
						"title" => "Opmerkingen",
						"data" => "description"
					]
				]
			);

			if (!is_null($schoolId)) {
				$rows = (new TemperatureRegistration)->getBySchoolAndDate($schoolId, $start, $end);
				Arrays::each($rows, fn ($row) => $row->link());
				$this->appendToJson("rows", $rows);
			} else $this->appendToJson('noRowsText', "Gelieve eerst te filteren");
		}
		$this->handle();
	}

	public function getPerson($view)
	{
		$schoolId = Helpers::input()->get('parentValue');

		if (!is_null($schoolId)) {
			$module = (new Module)->getByModule("temperatureregistration");
			$names = ((new ModuleSetting)->getByModuleAndKey($module->id, "names{$schoolId}"))->value;
			$names = json_decode($names, true) ?? $names;
			if (!is_array($names)) $names = [$names];

			$names = Arrays::map($names, fn ($n) => ["name" => $n]);
			$names = [
				["name" => SELECT_OTHER_VALUE],
				$names
			];
			$this->appendToJson("items", $names);
		}

		$this->handle();
	}

	public function getSettings($view)
	{
		$module = (new Module)->getByModule("temperatureregistration");
		$settings = (new ModuleSetting)->getByModule($module->id);

		$returnSettings = [];
		foreach ($settings as $setting) {
			$returnSettings[$setting->key] = json_decode($setting->value, true) ?? $setting->value;
			if (is_array($returnSettings[$setting->key])) $returnSettings[$setting->key] = implode(PHP_EOL, $returnSettings[$setting->key]);
		}

		$this->appendToJson("fields", $returnSettings);
		$this->handle();
	}

	//POST
	public function postSettings($view)
	{
		$module = (new Module)->getByModule('temperatureregistration');
		$moduleSettingRepo = new ModuleSetting;

		foreach (DEFAULT_SETTINGS["temperatureregistration"] as $setting => $defaultValue) {
			$moduleSetting = $moduleSettingRepo->getByModuleAndKey($module->id, $setting);
			$value = isset($_POST[$setting]) ? Helpers::input()->post($setting)->getValue() : $defaultValue;
			if (Strings::contains($value, PHP_EOL)) $value = json_encode(explode(PHP_EOL, $value));

			if (is_null($moduleSetting)) {
				$moduleSetting = new ObjectModuleSetting([
					'moduleId' => $module->id,
					'key' => $setting,
					'value' => Input::convertToBool($value)
				]);
			} else {
				$moduleSetting->value = Input::convertToBool($value);
			}

			$moduleSettingRepo->set($moduleSetting);
		}

		Log::write(description: "Changed settings for temperatureregistration");

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else {
			$this->setToast("Synchronisatie - Instellingen", "De instellingen zijn opgeslagen!");
		}
		$this->handle();
	}

	public function postExport()
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

		if (Input::empty($school[0])) {
			$this->setValidation("school", "Scholen moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Schools is not filled in");
		}
		if (!Input::check($start) || Input::empty($start)) {
			$this->setValidation("start", "Start datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "Start date is not filled in");
		}
		if (!Input::check($end) || Input::empty($end)) {
			$this->setValidation("end", "Eind datum moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
			Log::write(type: Log::TYPE_ERROR, description: "End dat is not filled in");
		}

		if ($this->validationIsAllGood()) {
			if (Clock::at($start)->isAfter(Clock::at($end))) {
				$this->setValidation("start", "Start datum moet voor de eind datum liggen!", self::VALIDATION_STATE_INVALID);
				$this->setValidation("end", "Start datum moet voor de eind datum liggen!", self::VALIDATION_STATE_INVALID);
				Log::write(type: Log::TYPE_ERROR, description: "Start date must be before end date");
			}

			if ($this->validationIsAllGood()) {
				if (Strings::equal($exportAs, 'pdf')) $this->exportPerSchoolAsPdf($school, $start, $end, $showNamesAs);
				else if (Strings::equal($exportAs, 'xlsx')) $this->exportPerSchoolAsXlsx($school, $start, $end, $showNamesAs);
				$this->setValidation("start", "", self::VALIDATION_STATE_VALID);
				$this->setValidation("end", "", self::VALIDATION_STATE_VALID);

				Log::write(description: "Export per school as $exportAs");
			}
		}

		if (!$this->validationIsAllGood()) $this->setHttpCode(400);
		$this->handle();
	}

	private function exportPerSchoolAsPdf($schoolIds, $start, $end, $showNamesAs)
	{
		$schoolRepo = new School();
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$zipFileName = "Temperatuurregistratie - Export Per School.zip";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

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
					"title" => "Dag",
					"border" => "B",
					"width" => 30
				];

				$table['header'][] = [
					"title" => "Datum",
					"border" => "LB",
					"width" => 25
				];

				$table['header'][] = [
					"title" => "Uur",
					"border" => "LB",
					"width" => 20
				];

				$table['header'][] = [
					"title" => "Soep",
					"border" => "LB",
					"width" => 15
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
							"text" => $events[$month]['day'],
							"border" => "T"
						];

						$table['data'][$user][] = [
							"text" => $events[$month]['date'],
							"border" => "LT"
						];

						$table['data'][$user][] = [
							"text" => $events[$month]['time'],
							"border" => "LT"
						];

						if (!is_null($events[$month]['soupTemp'])) {
							$table['data'][$user][] = [
								"text" => $events[$month]['soupTemp'] . " °C",
								"border" => "LT"
							];
						} else $table['data'][$user][] = ["border" => "LT"];

						if (!is_null($events[$month]['potatoRicePastaTemp'])) {
							$table['data'][$user][] = [
								"text" => $events[$month]['potatoRicePastaTemp'] . " °C",
								"border" => "LT"
							];
						} else $table['data'][$user][] = ["border" => "LT"];

						if (!is_null($events[$month]['vegetablesTemp'])) {
							$table['data'][$user][] = [
								"text" => $events[$month]['vegetablesTemp'] . " °C",
								"border" => "LT"
							];
						} else $table['data'][$user][] = ["border" => "LT"];

						if (!is_null($events[$month]['meatFishTemp'])) {
							$table['data'][$user][] = [
								"text" => $events[$month]['meatFishTemp'] . " °C",
								"border" => "LT"
							];
						} else $table['data'][$user][] = ["border" => "LT"];

						$table['data'][$user][] = [
							"text" => $events[$month]['name'],
							"border" => "LT"
						];

						$table['data'][$user][] = [
							"text" => $events[$month]['description'],
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

	private function exportPerSchoolAsXlsx($schoolIds, $start, $end, $showNamesAs)
	{
		$schoolRepo = new School();
		$folder = FileSystem::CreateFolder(LOCATION_DOWNLOAD . "/" . date("YmdHis"));
		$filename = "Temperatuurregistratie - Export Per School.xlsx";
		$monthsBetweenDates = Date::monthsBetweenDates($start, $end, "M Y");

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

							$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Dag", true, border: "b");
							$schoolColumn++;
							$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Datum", true, border: "b");
							$schoolColumn++;
							$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", "Uur", true, border: "b");
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
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['day'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['date'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['time'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['soupTemp'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['potatoRicePastaTemp'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['vegetablesTemp'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['meatFishTemp'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['name'], border: "t");
						$schoolColumn++;
						$excel->setCellValue($index, "{$schoolColumn}{$schoolRow}", $events[$month]['description'], border: "t");

						$schoolRow++;
					}
				}
			}
		}

		$excel->removeSheetByName('Worksheet');
		$excel->save();
		if ($this->validationIsAllGood()) $this->appendToJson("download", FileSystem::GetDownloadLink("{$folder}/{$filename}"));
	}

	private function getEventsGroupedByDateBySchoolId($schoolId, $start, $end, $showNamesAs)
	{
		$eventRepo = new TemperatureRegistration;
		$eventsGrouped = [];

		$events = $eventRepo->getBySchool($schoolId);
		$events = Arrays::filter($events, fn ($e) => Clock::at($e->date)->isAfterOrEqualTo(Clock::at($start)) && Clock::at($e->date)->isBeforeOrEqualTo(Clock::at($end)));

		foreach ($events as $event) {
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['day'] = $event->day;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['date'] = $event->date;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['time'] = $event->time;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['soupTemp'] = $event->soupTemp;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['potatoRicePastaTemp'] = $event->potatoRicePastaTemp;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['vegetablesTemp'] = $event->vegetablesTemp;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['meatFishTemp'] = $event->meatFishTemp;
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['name'] = ($showNamesAs == "initials" ? $event->personInitials : $event->person);
			$eventsGrouped[$event->id][Clock::at($event->date)->format("M Y")]['description'] = $event->description;
		}

		return $eventsGrouped;
	}
}
