<?php
namespace App\Helpers\class\pdfLayout\tcpdf;
use App\Helpers\class\pdfLayout\tcpdf\MagazinePdf;

class UserMagzinePdf 
{
    protected $flname;
    protected $options = [];
    
    public function __construct($fileName, $options = [])
    { 
        $this->flname = $fileName;
        $options = array_merge([
            'add_cover_page' => true,
            'left_margin' => 12,
            'top_margin' => 15,
            'right_margin' => 12,
            'columns' => 2,
            'column_gap' => 8,
        ], $options);
        $this->options = $options;
        
    }  
  
    public function generatePdf($magazineData)
    {
        $pdf = new MagazinePdf();
        if($this->options['add_cover_page']){
            $this->addCoverPage($pdf, $magazineData);
        }
        $_columnWidth = ($pdf->getW() - $pdf->getW() - $pdf->getLMargin() - $pdf->getGutter()) / 2;



        $pdf->setMagazine($magazineData);
        $pdf->SetCreator($magazineData['author'] ?? "YT Education");
        $pdf->SetAuthor($magazineData['author'] ?? "YT Education");
        $pdf->SetTitle($magazineData['title'] ?? "Question Bank");

        // Enable Unicode support
        $pdf->SetLanguageArray(array(
            'a_meta_charset' => 'UTF-8',
            'a_meta_dir' => 'ltr',
            'a_meta_language' => 'en'
        ));

        $leftMargin = $magazineData['options']['left_margin'] ?? 12;
        $topMargin = $magazineData['options']['top_margin'] ?? 15;
        $rightMargin = $magazineData['options']['right_margin'] ?? 12;
        $pdf->SetMargins($leftMargin, $topMargin, $rightMargin);
        $pdf->SetAutoPageBreak(true, 15);
        
        $pdf->AddPage();

        // Calculate columns
        $columnCount = $magazineData['columns'] ?? 2;
        $columnGap = $magazineData['column_gap'] ?? 8;
        $pdf->gutter = $columnGap;
        $pdf->columnCount = $columnCount;
        $pdf->columnWidth = ($pdf->getW() - $leftMargin - $rightMargin - ($columnGap * ($columnCount - 1))) / $columnCount;

        // Get options
        $language = $magazineData['language'] ?? 'both';
        $showSolutions = $magazineData['show_solutions'] ?? true;
        $showQuestionNumbers = $magazineData['show_question_numbers'] ?? true;
        $showCaDate = $magazineData['show_ca_date'] ?? true;
        $fontSize = $magazineData['font_size'] ?? 10;
        $colors = $magazineData['colors'] ?? [];
        
        // Build complete HTML content
        $html = $this->buildCompleteHtml($magazineData, $language, $showSolutions, $showQuestionNumbers, $showCaDate, $fontSize, $colors);
        
        // Set font with Unicode support - freesans has excellent Hindi/Devanagari support
        $pdf->SetFont('freesans', '', $fontSize, '', true);
        
        // Write HTML with Unicode support enabled
        // $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->PrintChapter(num : 1, title: "question Set",file: $html ,mode:true );

        $pdf->Output($this->flname, "F");
    }
    
    protected function buildCompleteHtml($magazineData, $language, $showSolutions, $showQuestionNumbers, $showCaDate, $fontSize, $colors): string
    {
        $textColor = $colors['text'] ?? '#333333';
        $accentColor = $colors['accent'] ?? '#0066cc';
        $headerColor = $colors['header'] ?? '#cc0000';
        
        // $html = '<meta charset="UTF-8">';
        $html = '<style>
            body, p, div, span { font-family: freesans; }
            p { margin: 2px 0; padding: 0; }
            .qnum { color: ' . $accentColor . '; font-weight: bold; font-size: ' . ($fontSize + 1) . 'pt; }
            .qtext { margin: 2px 0 3px 0; line-height: 1.4; }
            .opt { margin: 1px 0 1px 10px; font-size: ' . ($fontSize - 1) . 'pt; }
            .correct { color: ' . $headerColor . '; font-weight: bold; }
            .sol { margin: 3px 0 5px 10px; padding: 4px; background-color: #f5f5f5; font-size: ' . ($fontSize - 1) . 'pt; border-left: 2px solid ' . $accentColor . '; }
            .date { font-size: 7pt; color: #888; }
        </style>';
        
        $questions = $magazineData['questions'] ?? collect();
        $questionNumber = 1;
        
        foreach ($questions as $question) {
            // Skip headers - we don't need category repetition
            if (isset($question->is_subject_header) || isset($question->is_topic_header)) {
                continue;
            }
            
            // Question number with CA date
            if ($showQuestionNumbers) {
                $html .= '<p class="qnum">Q' . $questionNumber . '.';
                if ($showCaDate && !empty($question->ca_date)) {
                    $html .= ' <span class="date">(' . $question->ca_date . ')</span>';
                }
                $html .= '</p>';
            }
            
            // Question text - no htmlspecialchars to preserve Unicode
            $html .= '<p class="qtext">';
            if ($language === 'both') {
                $qEn = $this->cleanText($question->question_text_en ?? '');
                $qHi = $this->cleanText($question->question_text_hi ?? '');
                if ($qEn && $qHi) {
                    $html .= $qEn . ' / ' . $qHi;
                } elseif ($qEn) {
                    $html .= $qEn;
                } elseif ($qHi) {
                    $html .= $qHi;
                }
            } elseif ($language === 'english') {
                $html .= $this->cleanText($question->question_text_en ?? '');
            } else {
                $html .= $this->cleanText($question->question_text_hi ?? '');
            }
            $html .= '</p>';
            
            // Options
            if (!empty($question->formattedQuestion->options)) {
                foreach ($question->formattedQuestion->options as $index => $option) {
                    $optLabel = chr(65 + $index);
                    $isCorrect = $option->is_correct ?? false;
                    $class = $isCorrect ? 'opt correct' : 'opt';
                    
                    $html .= '<p class="' . $class . '">(' . $optLabel . ') ';
                    
                    if ($language === 'both') {
                        $oEn = $this->cleanText($option->text->en ?? '');
                        $oHi = $this->cleanText($option->text->hi ?? '');
                        if ($oEn && $oHi) {
                            $html .= $oEn . ' / ' . $oHi;
                        } elseif ($oEn) {
                            $html .= $oEn;
                        } elseif ($oHi) {
                            $html .= $oHi;
                        }
                    } elseif ($language === 'english') {
                        $html .= $this->cleanText($option->text->en ?? '');
                    } else {
                        $html .= $this->cleanText($option->text->hi ?? '');
                    }
                    
                    $html .= '</p>';
                }
            }
            
            // Solution
            if ($showSolutions && !empty($question->formattedQuestion->solution)) {
                $solution = $question->formattedQuestion->solution;
                $html .= '<p class="sol"><strong>Sol:</strong> ';
                
                if ($language === 'both') {
                    $sEn = $this->cleanText($solution->text->en ?? '');
                    $sHi = $this->cleanText($solution->text->hi ?? '');
                    if ($sEn && $sHi) {
                        $html .= $sEn . ' / ' . $sHi;
                    } elseif ($sEn) {
                        $html .= $sEn;
                    } elseif ($sHi) {
                        $html .= $sHi;
                    }
                } elseif ($language === 'english') {
                    $html .= $this->cleanText($solution->text->en ?? '');
                } else {
                    $html .= $this->cleanText($solution->text->hi ?? '');
                }
                
                $html .= '</p>';
            }
            
            $html .= '<p style="margin: 0 0 6px 0;"></p>'; // Small gap between questions
            $questionNumber++;
        }
        
        return $html;
    }

    
    protected function cleanText($text): string
    {
        if (is_array($text)) {
            $text = implode(' ', $text);
        }
        // Remove HTML tags but preserve Unicode characters
        $text = strip_tags($text);
        // Decode any HTML entities to actual characters
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim($text);
    }


    protected function addCoverPage($pdf, $magazineData)
    {
        $pdf->AddPage();
        $pdf->SetFont('freesans', 'B', 20, '', true);
        $title = $magazineData['title'] ?? 'QUESTION BANK';
        $author = $magazineData['author'] ?? 'YT Education';
        $date = date('F d, Y');
        
        // Title
        $pdf->SetY(80);
        $pdf->Cell(0, 10, $title, 0, 1, 'C');
        
        // Author
        $pdf->SetFont('freesans', '', 16, '', true);
        $pdf->Cell(0, 10, 'Author: ' . $author, 0, 1, 'C');
        
        // Date
        $pdf->SetFont('freesans', '', 14, '', true);
        $pdf->Cell(0, 10, 'Date: ' . $date, 0, 1, 'C');
        
        // Add a page break after cover
        $pdf->AddPage();
    }
}
