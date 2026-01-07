<?php
namespace App\Helpers\class\pdfLayout\tcpdf;

use TCPDF;

class MagazinePdf extends TCPDF 
{
    protected $magazine;
    public $gutter = 10;
    public $currentColumn = 0;
    public $columnWidth;
    public $columnCount = 2;

    public function __construct() {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        // Enable font subsetting for better Unicode support
        $this->setFontSubsetting(true);
    }

    public function setMagazine($magazine){
        $this->magazine = $magazine;
    }

    function Header(){
        $this->SetY(15);
        $this->SetFont('freesans', 'B', 11, '', true);
        $title = $this->magazine['title'] ?? 'QUESTION BANK';
        $this->Cell(0, 8, $title, 0, 0, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('freesans', '', 8, '', true);
        $this->Cell(0, 10, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    public function startColumnMode(){
        $this->currentColumn = 0;
        $this->SetY($this->tMargin + 18);
    }

    public function PrintChapter($num, $title, $file, $mode=false) {
		// add a new page
		$this->AddPage();
		// disable existing columns
		$this->resetColumns();
		// print chapter title
		$this->ChapterTitle($num, $title);
		// set columns
		$this->setEqualColumns(3, 57);
		// print chapter body
		$this->ChapterBody($file, $mode);
	}
    public function ChapterTitle($num, $title) {
		$this->SetFont('helvetica', '', 14);
		$this->SetFillColor(200, 220, 255);
		$this->Cell(180, 6, 'Chapter '.$num.' : '.$title, 0, 1, '', 1);
		$this->Ln(4);
	}
    public function ChapterBody($file, $mode=false) {
		$this->selectColumn();
		// get esternal file content
		// $content = file_get_contents($file, false);
        $content = $file;
		// set font
		$this->SetFont('times', '', 9);
		$this->SetTextColor(50, 50, 50);
		// print content
		if ($mode) {
			// ------ HTML MODE ------
			// $this->writeHTML($content, true, false, true, false, 'J');
			$this->writeHTML($content, true, false, true, false, 'J');
		} else {
			// ------ TEXT MODE ------
			$this->Write(0, $content, '', 0, 'J', true, 0, false, true, 0);
		}
		$this->Ln();
	}

    

    public function getW() { return $this->w; }
    public function getLMargin() { return $this->lMargin; }
    public function getRMargin() { return $this->rMargin; }
    public function getTMargin() { return $this->tMargin; }
    public function getBMargin() { return $this->bMargin; }
    public function getGutter() { return $this->gutter; }
    public function getPageBreakTrigger() { return $this->PageBreakTrigger; }
}