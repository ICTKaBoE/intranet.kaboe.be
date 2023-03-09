<?php

namespace Helpers;

use Ouzo\Utilities\Strings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class Excel
{
	private Spreadsheet $spreadsheet;

	public function __construct($location)
	{
		$this->location = $location;
		$this->spreadsheet = new Spreadsheet();
	}

	public function createSheet($index, $title)
	{
		$this->spreadsheet->createSheet($index);
		$this->setSheetTitle($index, $title);
	}

	public function setSheetTitle($index, $title)
	{
		$sheet = $this->spreadsheet->getSheet($index);
		$sheet->setTitle($title);
	}

	public function setCellValue($sheetIndex, $range, $value, $bold = false, $size = 11, $border = "", $borderStyle = Border::BORDER_THIN, $link = false)
	{
		$sheet = $this->spreadsheet->getSheet($sheetIndex);
		if (Strings::contains($range, ":")) {
			$sheet->mergeCells($range);
			$range = explode(":", $range)[0];
		}

		if (is_array($value)) $sheet->fromArray($value, NULL, $range);
		else $sheet->setCellValue($range, $value);
		$sheet->getStyle($range)->getFont()->setBold($bold);
		$sheet->getStyle($range)->getFont()->setSize($size);

		$border = str_split($border);
		foreach ($border as $b) {
			if ($b === "t") $sheet->getStyle($range)->getBorders()->getTop()->setBorderStyle($borderStyle);
			if ($b === "b") $sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle($borderStyle);
			if ($b === "l") $sheet->getStyle($range)->getBorders()->getLeft()->setBorderStyle($borderStyle);
			if ($b === "r") $sheet->getStyle($range)->getBorders()->getRight()->setBorderStyle($borderStyle);
		}

		if ($link) $sheet->getCell($range)->getHyperlink()->setUrl($link);
	}

	public function save()
	{
		for ($i = 0; $i < $this->spreadsheet->getSheetCount(); $i++) {
			foreach ($this->spreadsheet->getSheet($i)->getColumnIterator() as $c) {
				$this->spreadsheet->getSheet($i)
					->setSelectedCell("A1")
					->getColumnDimension($c->getColumnIndex())
					->setAutoSize(true);
			}

			$this->spreadsheet
				->getSheet($i)
				->setSelectedCell("A1")
				->getSheetView()
				->setZoomScale(100);

			$this->spreadsheet
				->getSheet($i)
				->getPageSetup()
				->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
				->setPaperSize(PageSetup::PAPERSIZE_A4)
				->setFitToWidth(1)
				->setFitToHeight(0);
		}

		$this->spreadsheet->setActiveSheetIndex(0);

		$writer = new Xlsx($this->spreadsheet);
		$writer->save($this->location);
	}
}
