<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\CheckStudentRelationInsz as ObjectCheckStudentRelationInsz;
use Database\Repository\CheckStudentRelationInsz;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Router\Helpers;
use Security\FileSystem;

class CheckController extends ApiController
{
	public function studentRelation()
	{
		$linkToFile = LOCATION_BACKEND . "/externalfiles/CheckStudentRelation.xlsx";
		$reader = IOFactory::createReader("Xlsx");
		$reader->setReadDataOnly(TRUE);
		$spreadsheet = $reader->load($linkToFile);
		$spreadsheet->setActiveSheetIndexByName("Form1");
		$worksheet = $spreadsheet->getActiveSheet();
		$checkStudentRelationRepo = new CheckStudentRelationInsz;

		$data = [];
		$headers = [];

		foreach ($worksheet->getRowIterator() as $rindex => $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE);

			if ($row->getRowIndex() - 1 == 0) {
				foreach ($cellIterator as $index => $cell) {
					Arrays::setNestedValue($headers, [$index], $cell->getValue());
				}
			} else {
				foreach ($cellIterator as $index => $cell) {
					$data[$row->getRowIndex()][$headers[$index]] = $cell->getValue();
				}
			}
		}

		$data = array_values($data);

		foreach ($data as $index => $row) {
			$checkStudentRelation = $checkStudentRelationRepo->getByInsz($row['Rijksregister kind']) ?? new ObjectCheckStudentRelationInsz;
			if ($checkStudentRelation->locked || $checkStudentRelation->published) $checkStudentRelation = new ObjectCheckStudentRelationInsz;

			$checkStudentRelation->school = Arrays::getValue($row, "Op welke school zit uw kind?", $checkStudentRelation->school);
			$checkStudentRelation->class = Arrays::getValue($row, "In welke klas zit uw kind?", false) ?? Arrays::getValue($row, "In welke klas zit uw kind?2", false) ?? Arrays::getValue($row, "In welke klas zit uw kind?3", false) ?? Arrays::getValue($row, "In welke klas zit uw kind?4", $checkStudentRelation->class);
			$checkStudentRelation->childName = Arrays::getValue($row, "Naam kind", $checkStudentRelation->childName);
			$checkStudentRelation->childInsz = Arrays::getValue($row, "Rijksregister kind", $checkStudentRelation->childInsz);
			$checkStudentRelation->motherName = Arrays::getValue($row, "Naam moeder", $checkStudentRelation->motherName);
			$checkStudentRelation->motherInsz = Arrays::getValue($row, "Rijksregister moeder", $checkStudentRelation->motherInsz);
			$checkStudentRelation->fatherName = Arrays::getValue($row, "Naam vader", $checkStudentRelation->fatherName);
			$checkStudentRelation->fatherInsz = Arrays::getValue($row, "Rijksregister vader", $checkStudentRelation->fatherInsz);
			$checkStudentRelation->insertDateTime = Clock::nowAsString("Y-m-d H:i:s");

			$checkStudentRelationRepo->set($checkStudentRelation);
		}

		$this->handle();
	}
}
