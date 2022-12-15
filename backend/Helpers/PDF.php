<?php

namespace Helpers;

use Ouzo\Utilities\Arrays;
use TCPDF;

class PDF extends TCPDF
{
	public function __construct($title, $location, $orientation = "P", $headerText = "")
	{
		$this->title = $title;
		$this->location = $location;
		$this->headerText = $headerText;

		parent::__construct($orientation);
		$this->setCreator("Appliction");
		$this->setTitle($this->title);
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	}

	public function Header()
	{
		$this->SetFont('helvetica', 'B', 20);
		$this->Cell(0, 20, $this->headerText, 0, false, 'C', 0, '', 0, false, 'M', 'B');
	}

	public function table($table)
	{
		$header = $table['header'] ?? [];
		$data = $table['data'] ?? [];
		$widths = [];
		$knownWidth = $unknownWidth = 0;

		foreach ($header as $h) {
			$widths[] = $h['width'] ?? false;
			if ($h['width'] ?? false == false) $unknownWidth++;
			else $knownWidth += $h['width'];
		}

		for ($i = 0; $i < count($widths); $i++) {
			if ($widths[$i] == false) {
				$widths[$i] = round(($this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT - $knownWidth) / $unknownWidth, mode: PHP_ROUND_HALF_DOWN);
			}
		}

		$this->setFont("helvetica", "B");
		foreach ($header as $i => $h) {
			$title = $h['title'] ?? "";
			$border = $h['border'] ?? false;

			$this->Cell($widths[$i], 0, $title, $border, 0, 'L', false, '', 0, false, 'L', 'C');
		}

		$this->Ln();

		$this->setFont("helvetica");
		foreach ($data as $row) {
			foreach ($row as $i => $r) {
				$text = is_array($r) ? $r['text'] : $r;
				$border = is_array($r) ? $r['border'] ?? false : false;
				$this->Cell($widths[$i], 0, $text, $border, 0, 'L', false, '', 0, false, 'L', 'C');
			}

			$this->Ln();
		}
	}

	public function save()
	{
		$this->Output($this->location, "F");
	}
}
