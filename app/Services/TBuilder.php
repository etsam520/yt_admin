<?php 
namespace App\Services ;

use TCPDF;

class TBuilder extends TCPDF {
    // You can add custom methods here if needed
    // For example, custom headers or footers
    
    // Header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->Cell(0, 10, 'Questions PDF', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
    }
    
    // Footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 
            0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}