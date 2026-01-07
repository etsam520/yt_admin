<?php
namespace App\Helpers\class\pdfLayout\tcpdf;

use TCPDF;

class MagazinePdf2nd extends TCPDF 
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

    

    public function getW() { return $this->w; }
    public function getLMargin() { return $this->lMargin; }
    public function getRMargin() { return $this->rMargin; }
    public function getTMargin() { return $this->tMargin; }
    public function getBMargin() { return $this->bMargin; }
    public function getGutter() { return $this->gutter; }
    public function getPageBreakTrigger() { return $this->PageBreakTrigger; }
}