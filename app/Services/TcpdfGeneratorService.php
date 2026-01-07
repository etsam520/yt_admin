<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mockery\Matcher\Any;
use TCPDF;

/**
 * TCPDF-based PDF Generator Service
 * Generates professional multi-column PDFs with mathematical formula support
 */
class TcpdfGeneratorService
{
    protected $tempPath;
    protected $mathRendererService;

    public function __construct(MathRendererService $mathRenderer = null)
    {
        $this->tempPath = storage_path('app/temp/pdf/');
        $this->mathRendererService = $mathRenderer ?? new MathRendererService();

        // Ensure temp directory exists
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    /**
     * Generate PDF from questions using TCPDF
     */
    public function generateQuestionsPdf(Collection $questions, string $template, array $options, string $language = 'both', bool $isPreview=false)
    {
        try {
            // Process questions
            $processedQuestions = $this->processImagesForPdf($questions);

            if ($options['render_math'] ?? true) {
                $processedQuestions = $this->processMathFormulas($processedQuestions);
            }

            // Create PDF based on template
            // create new PDF document
            $pdf = new TBuilder(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Your Application');
            $pdf->SetTitle('Questions PDF');
            $pdf->SetSubject('Questions');

            // Set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // Set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // Set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // Set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // Set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // Set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // Add cover page if requested
            if ($options['show_cover_page'] ?? false) {
                $this->addCoverPage($pdf, $options);
            }

            // Add a page for questions
            $pdf->AddPage();

            // Add content using QuestionLayout
            $pdf->resetColumns();
            $pdf->SetFont('helvetica', '', 14);
            $pdf->SetFillColor(200, 220, 255);
            $pdf->Cell(180, 6, 'Chapter '.' : fdfdf', 0, 1, '', 1);
            $chapterSpacing = max(0.5, min(5, $options['chapter_spacing'] ?? 1.5)); // Safe range: 0.5-5mm
            $pdf->Ln($chapterSpacing);
            $pdf->setEqualColumns(2, 90);
            $questionNumber = 1;
            foreach ($processedQuestions as $question) {
                $questionLayout = new QuestionLayout($pdf, $question, $questionNumber, $options, $language );
                $questionLayout->processQuestion();
                $questionNumber++;
                // Add validated space between questions
                $questionSpacing = max(1, min(8, $options['question_spacing'] ?? 2)); // Safe range: 1-8mm
                $pdf->Ln($questionSpacing);
            }

            // Generate file name
            $fileName = uniqid('questions_') . '.pdf';
            $pdfFile = $this->tempPath . DIRECTORY_SEPARATOR . $fileName;

            // Ensure temp directory exists
            if (!file_exists($this->tempPath)) {
                mkdir($this->tempPath, 0755, true);
            }

            // Output PDF to file
            $pdf->Output($pdfFile, 'F');

            // Check if PDF was created
            if (!file_exists($pdfFile) || filesize($pdfFile) === 0) {
                throw new \Exception("PDF file was not generated or is empty");
            }

            // Get content and delete temp file
            $content = file_get_contents($pdfFile);
            unlink($pdfFile);

            if($isPreview){
                return response(
                    content: $content,
                    status: 200,
                    headers: [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="questions.pdf"',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0'
                    ]
                );
            }

            // Move to storage
            $storagePath = 'pdfs/' . date('Y/m/d/') . basename($pdfFile);
            Storage::disk('public')->put($storagePath, $content);
            return $storagePath;

        } catch (\Exception $e) {
            Log::error('TCPDF Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Create TCPDF instance with custom settings
     */
    protected function createTcpdfInstance(array $options): TCPDF
    {
        // Page size mapping
        $pageSizeMap = [
            'A4' => 'A4',
            'A3' => 'A3',
            'Letter' => 'LETTER',
            'Legal' => 'LEGAL',
        ];

        $pageSize = $pageSizeMap[$options['page_size'] ?? 'A4'] ?? 'A4';
        $orientation = ($options['orientation'] ?? 'portrait') === 'landscape' ? 'L' : 'P';

        // Create PDF
        $pdf = new TCPDF($orientation, 'mm', $pageSize, true, 'UTF-8', false);

        // Set margins
        $margin = $this->parseSize($options['page_padding'] ?? '15mm');
        $pdf->SetMargins($margin, $margin, $margin);
        $pdf->SetAutoPageBreak(true, $margin);

        // Set font
        $pdf->SetFont('dejavusans', '', $options['font_size'] ?? 11);

        // Set colors
        $pdf->SetTextColor(
            ...$this->hexToRgb($options['text_color'] ?? '#1a1a1a')
        );

        // Set default line width
        $pdf->SetLineWidth(0.3);

        // Enable auto-break
        $pdf->SetAutoPageBreak(true, $margin);

        return $pdf;
    }

    /**
     * Add content to PDF with multi-column layout simulation
     */
    protected function addContentToPdf(TCPDF &$pdf, Collection $questions, array $options, string $language, string $template): void
    {
        $pdf->AddPage();

        // Add header
        if (!empty($options['header_text'])) {
            $this->addHeader($pdf, $options, $language);
        }

        // Column settings
        $columnCount = $options['column_count'] ?? 2;
        $columnGap = $this->parseSize($options['column_gap'] ?? '8mm');

        // Calculate column width
        $pageWidth = $pdf->GetPageWidth() - (2 * $this->parseSize($options['page_padding'] ?? '15mm'));
        $columnWidth = ($pageWidth - ($columnCount - 1) * $columnGap) / $columnCount;

        // Process questions
        $columnIndex = 0;
        $currentColumnHeight = 0;
        $maxColumnHeight = $pdf->GetPageHeight() - 80; // Leaving space for header/footer
        $columnStartY = $pdf->GetY();

        $questionNumber = 1;
        $answerKey = [];

        foreach ($questions as $question) {
            if (isset($question->is_subject_header)) {
                // Subject header - span all columns
                $this->addSectionHeader($pdf, $question, $columnWidth, $columnCount, $columnGap);
                $columnIndex = 0;
                $currentColumnHeight = 0;
                $columnStartY = $pdf->GetY();

            } elseif (isset($question->is_topic_header)) {
                // Topic header - span all columns
                $this->addSectionHeader($pdf, $question, $columnWidth, $columnCount, $columnGap);
                $columnIndex = 0;
                $currentColumnHeight = 0;
                $columnStartY = $pdf->GetY();

            } else {
                // Regular question
                $questionHeight = $this->estimateQuestionHeight(
                    $pdf,
                    $question,
                    $columnWidth,
                    $options,
                    $language
                );

                // Check if we need to move to next column or page
                if ($currentColumnHeight + $questionHeight > $maxColumnHeight) {
                    $columnIndex++;

                    if ($columnIndex >= $columnCount) {
                        // Move to next page
                        $pdf->AddPage();
                        $columnIndex = 0;
                        $currentColumnHeight = 0;
                        $columnStartY = $pdf->GetY();
                    } else {
                        // Move to next column on same page
                        $columnStartX = $this->parseSize($options['page_padding'] ?? '15mm') + 
                                       ($columnIndex * ($columnWidth + $columnGap));
                        $pdf->SetXY($columnStartX, $columnStartY);
                    }
                }

                // Add question to current column
                $this->addQuestionBlock(
                    $pdf,
                    $question,
                    $questionNumber,
                    $columnWidth,
                    $options,
                    $language,
                    $answerKey
                );

                $currentColumnHeight += $questionHeight;
                $questionNumber++;
            }
        }

        // Add answer key if requested
        if ($options['show_answer_key'] ?? false) {
            $pdf->AddPage();
            $this->addAnswerKeySection($pdf, $answerKey, $options);
        }

        // Add footer if specified
        if (!empty($options['footer_text'])) {
            $this->addFooter($pdf, $options);
        }
    }

    /**
     * Add header to PDF
     */
    protected function addHeader(TCPDF &$pdf, array $options, string $language): void
    {
        $pdf->SetFont('dejavusans', 'B', 18);
        $pdf->Cell(0, 15, $options['header_text'] ?? 'Question Paper', 0, 1, 'C');

        $pdf->SetFont('dejavusans', '', 10);
        $meta = "Generated: " . date('Y-m-d H:i:s');
        if ($language === 'both') {
            $meta .= " | द्विभाषी / Bilingual";
        } elseif ($language === 'hindi') {
            $meta .= " | हिंदी";
        }
        $pdf->Cell(0, 8, $meta, 0, 1, 'C');

        $pdf->SetDrawColor(44, 90, 160);
        $pdf->SetLineWidth(1);
        $pdf->Line(15, $pdf->GetY(), $pdf->GetPageWidth() - 15, $pdf->GetY());
        $pdf->SetLineWidth(0.3);

        $pdf->Ln(5);
    }

    /**
     * Add section header (Subject/Topic)
     */
    protected function addSectionHeader(TCPDF &$pdf, object $section, float $columnWidth, int $columnCount, float $columnGap): void
    {
        $margin = $this->parseSize('15mm');
        $totalWidth = $pdf->GetPageWidth() - (2 * $margin);

        $pdf->SetFillColor(44, 90, 160);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('dejavusans', 'B', 14);

        $headerText = $section->subject_name ?? '';
        if (isset($section->topic_name)) {
            $headerText .= ' - ' . $section->topic_name;
        }

        $pdf->Cell($totalWidth, 10, $headerText, 0, 1, 'L', true);

        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->Cell($totalWidth, 6, ($section->question_count ?? 0) . ' प्रश्न / Questions', 0, 1, 'L', true);

        // Reset colors
        $pdf->SetTextColor(26, 26, 26);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Ln(3);
    }

    /**
     * Add a question block
     */
    protected function addQuestionBlock(TCPDF &$pdf, object $question, int $number, float $columnWidth, array $options, string $language, array &$answerKey): void
    {
        // Question header with number
        $pdf->SetFont('dejavusans', 'B', ($options['font_size'] ?? 11) + 1);

        if ($options['show_question_numbers'] ?? true) {
            $pdf->SetFillColor(44, 90, 160);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(10, 8, $number, 1, 0, 'C', true);
            $pdf->SetTextColor(26, 26, 26);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell($columnWidth - 10, 8, '', 0, 1);
        }

        // Question text
        $pdf->SetFont('dejavusans', '', $options['font_size'] ?? 11);

        if ($language === 'hindi' || $language === 'both') {
            $questionText = $question->question_text_hi ?? $question->formattedQuestion->question->text->hi ?? '';
            if (!empty($questionText)) {
                $pdf->MultiCell($columnWidth, 5, $this->cleanHtmlTags($questionText), 0, 'L');
            }
        }

        if ($language === 'both') {
            $pdf->Ln(2);
        }

        if ($language === 'english' || $language === 'both') {
            $questionText = $question->question_text_en ?? $question->formattedQuestion->question->text->en ?? '';
            if (!empty($questionText)) {
                $pdf->MultiCell($columnWidth, 5, $this->cleanHtmlTags($questionText), 0, 'L');
            }
        }

        // Options
        if (isset($question->formattedQuestion->options)) {
            $pdf->Ln(3);

            foreach ($question->formattedQuestion->options as $index => $option) {
                $optionLabel = chr(65 + $index);
                $isCorrect = ($options['highlight_correct_answers'] ?? false) &&
                            (strtoupper($question->formattedQuestion->answer ?? '') === $optionLabel);

                if ($isCorrect) {
                    $answerKey[$number] = $optionLabel;
                }

                // Option label
                $pdf->SetFont('dejavusans', 'B', $options['font_size'] ?? 11);
                if ($isCorrect) {
                    $pdf->SetFillColor(232, 245, 233);
                }
                $pdf->Cell(8, 5, $optionLabel . '.', 0, 0);

                // Option text
                $pdf->SetFont('dejavusans', '', $options['font_size'] ?? 11);
                $optionText = '';

                if ($language === 'hindi' || $language === 'both') {
                    $optionText .= $option->text->hi ?? '';
                }

                if ($language === 'both') {
                    $optionText .= ' / ';
                }

                if ($language === 'english' || $language === 'both') {
                    $optionText .= $option->text->en ?? '';
                }

                $pdf->MultiCell($columnWidth - 10, 5, $this->cleanHtmlTags($optionText), 0, 'L');

                if ($isCorrect) {
                    $pdf->SetFillColor(255, 255, 255);
                }
            }
        }

        // Solution
        if (($options['show_solutions'] ?? false) && isset($question->formattedQuestion->solution)) {
            if (!($options['answers_on_separate_page'] ?? false)) {
                $pdf->Ln(2);
                $pdf->SetFont('dejavusans', 'B', ($options['font_size'] ?? 11) - 1);
                $pdf->SetTextColor(255, 152, 0);
                $pdf->Cell($columnWidth, 5, '💡 समाधान / Solution:', 0, 1);

                $pdf->SetFont('dejavusans', '', ($options['font_size'] ?? 11) - 1);
                $pdf->SetTextColor(26, 26, 26);

                $solutionText = '';
                if ($language === 'hindi' || $language === 'both') {
                    $solutionText .= $question->formattedQuestion->solution->text->hi ?? '';
                }

                if ($language === 'both') {
                    $solutionText .= ' / ';
                }

                if ($language === 'english' || $language === 'both') {
                    $solutionText .= $question->formattedQuestion->solution->text->en ?? '';
                }

                $pdf->MultiCell($columnWidth, 5, $this->cleanHtmlTags($solutionText), 0, 'L');
            }
        }

        $pdf->Ln(5);
    }

    /**
     * Add answer key section
     */
    protected function addAnswerKeySection(TCPDF &$pdf, array $answerKey, array $options): void
    {
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, 'उत्तर कुंजी / Answer Key', 0, 1, 'C');
        $pdf->Ln(5);

        // Create answer key grid (6 columns per row)
        $pdf->SetFont('dejavusans', '', 11);
        $cellWidth = ($pdf->GetPageWidth() - 30) / 6;
        $rowCount = 0;

        foreach ($answerKey as $qNum => $answer) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell($cellWidth / 2, 8, 'Q' . $qNum, 1, 0, 'C', true);
            $pdf->SetFillColor(220, 250, 220);
            $pdf->Cell($cellWidth / 2, 8, $answer, 1, 0, 'C', true);

            $rowCount++;
            if ($rowCount % 6 == 0) {
                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
        }

        $pdf->Ln();
    }

    /**
     * Add cover page
     */
    protected function addCoverPage($pdf, array $options): void
    {
        $pdf->AddPage();
        
        $coverOptions = $options['cover_page'] ?? [];
        $pageWidth = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();
        
        // Add background color or gradient
        $pdf->SetFillColor(245, 248, 252); // Light blue background
        $pdf->Rect(0, 0, $pageWidth, $pageHeight, 'F');
        
        // Add decorative header bar
        $pdf->SetFillColor(44, 90, 160); // Dark blue
        $pdf->Rect(0, 0, $pageWidth, 25, 'F');
        
        // Add dummy logo/image if provided or create a placeholder
        $imageY = 40;
        $imageSize = 60;
        $centerX = $pageWidth / 2;
        
        if (!empty($coverOptions['image_path']) && file_exists($coverOptions['image_path'])) {
            $pdf->Image($coverOptions['image_path'], $centerX - ($imageSize/2), $imageY, $imageSize, $imageSize);
        } else {
            // Create a placeholder logo
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetDrawColor(44, 90, 160);
            $pdf->SetLineWidth(2);
            $logoX = $centerX - ($imageSize/2);
            $logoY = $imageY;
            $pdf->Rect($logoX, $logoY, $imageSize, $imageSize, 'FD');
            
            // Add placeholder text in logo
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetTextColor(44, 90, 160);
            $pdf->SetXY($logoX, $logoY + 20);
            $pdf->Cell($imageSize, 20, 'LOGO', 0, 0, 'C');
        }
        
        // Main title (Set Name)
        $titleY = $imageY + $imageSize + 30;
        $titleColor = $this->hexToRgb($coverOptions['title_color'] ?? '#2c5aa0');
        $pdf->SetTextColor(...$titleColor);
        $pdf->SetFont('helvetica', 'B', 28);
        
        $setName = $coverOptions['set_name'] ?? 'Question Set';
        $pdf->SetY($titleY);
        $pdf->Cell(0, 20, $setName, 0, 1, 'C');
        
        // Subtitle
        $pdf->SetFont('helvetica', '', 16);
        $subtitleColor = $this->hexToRgb($coverOptions['subtitle_color'] ?? '#6b7280');
        $pdf->SetTextColor(...$subtitleColor);
        $pdf->Cell(0, 12, 'Professional Question Paper', 0, 1, 'C');
        
        // Add decorative line
        $pdf->SetDrawColor(44, 90, 160);
        $pdf->SetLineWidth(1);
        $lineY = $pdf->GetY() + 10;
        $lineWidth = 100;
        $pdf->Line($centerX - ($lineWidth/2), $lineY, $centerX + ($lineWidth/2), $lineY);
        
        // Teacher and Date information
        $infoY = $lineY + 30;
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(44, 90, 160);
        $pdf->SetY($infoY);
        
        // Teacher Name
        if (!empty($coverOptions['teacher_name'])) {
            $pdf->Cell(0, 10, 'Prepared by: ' . $coverOptions['teacher_name'], 0, 1, 'C');
            $pdf->Ln(5);
        }
        
        // Date
        $date = $coverOptions['date'] ?? date('F j, Y');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 8, 'Date: ' . $date, 0, 1, 'C');
        
        // Add footer decoration
        $footerY = $pageHeight - 40;
        $pdf->SetDrawColor(44, 90, 160);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(20, $footerY, $pageWidth - 20, $footerY);
        
        $pdf->SetY($footerY + 5);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->Cell(0, 6, 'Generated with EasyWay PDF Generator', 0, 1, 'C');
        
        // Add some decorative elements
        $this->addCoverDecorations($pdf, $pageWidth, $pageHeight);
    }
    
    /**
     * Add decorative elements to cover page
     */
    protected function addCoverDecorations(TCPDF &$pdf, float $pageWidth, float $pageHeight): void
    {
        // Add corner decorations
        $pdf->SetDrawColor(200, 220, 255);
        $pdf->SetLineWidth(3);
        
        // Top-left corner
        $pdf->Line(15, 30, 40, 30);
        $pdf->Line(15, 30, 15, 55);
        
        // Top-right corner
        $pdf->Line($pageWidth - 40, 30, $pageWidth - 15, 30);
        $pdf->Line($pageWidth - 15, 30, $pageWidth - 15, 55);
        
        // Bottom-left corner
        $pdf->Line(15, $pageHeight - 55, 15, $pageHeight - 30);
        $pdf->Line(15, $pageHeight - 30, 40, $pageHeight - 30);
        
        // Bottom-right corner
        $pdf->Line($pageWidth - 15, $pageHeight - 55, $pageWidth - 15, $pageHeight - 30);
        $pdf->Line($pageWidth - 40, $pageHeight - 30, $pageWidth - 15, $pageHeight - 30);
    }

    /**
     * Add footer
     */
    protected function addFooter(TCPDF &$pdf, array $options): void
    {
        $pdf->SetY($pdf->GetPageHeight() - 10);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, $options['footer_text'], 0, 1, 'C');
    }

    /**
     * Estimate question height in mm
     */
    protected function estimateQuestionHeight(TCPDF &$pdf, object $question, float $columnWidth, array $options, string $language): float
    {
        // Rough estimate: 8mm header + text height + options height + solution height
        $height = 8; // Header

        // Question text
        if ($language === 'hindi' || $language === 'both') {
            $questionText = $question->question_text_hi ?? '';
            if (!empty($questionText)) {
                $height += $this->estimateTextHeight($pdf, $questionText, $columnWidth);
            }
        }

        if ($language === 'english' || $language === 'both') {
            $questionText = $question->question_text_en ?? '';
            if (!empty($questionText)) {
                $height += $this->estimateTextHeight($pdf, $questionText, $columnWidth);
            }
        }

        // Options (roughly 5mm per option)
        if (isset($question->formattedQuestion->options)) {
            $height += count($question->formattedQuestion->options) * 5;
        }

        // Solution
        if (isset($question->formattedQuestion->solution)) {
            $height += 5;
            $solutionText = $question->formattedQuestion->solution->text->en ?? '';
            $height += $this->estimateTextHeight($pdf, $solutionText, $columnWidth);
        }

        return $height + 5; // Add padding
    }

    /**
     * Estimate text height when rendered
     */
    protected function estimateTextHeight(TCPDF &$pdf, string $text, float $width): float
    {
        $cleanText = $this->cleanHtmlTags($text);
        $lines = ceil(strlen($cleanText) / ($width / 2.5)); // Rough estimate
        return $lines * 4; // Approximately 4mm per line
    }

    /**
     * Clean HTML tags from text
     */
    protected function cleanHtmlTags(string $text): string
    {
        // Remove common HTML tags
        $text = preg_replace('/<[^>]*>/', ' ', $text);
        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Parse size string (e.g., "15mm", "1cm") to mm
     */
    protected function parseSize(string $size): float
    {
        preg_match('/(\d+\.?\d*)\s*(mm|cm|in|pt)/', $size, $matches);

        if (!isset($matches[1])) {
            return 15; // Default
        }

        $value = (float)$matches[1];
        $unit = $matches[2] ?? 'mm';

        return match ($unit) {
            'cm' => $value * 10,
            'in' => $value * 25.4,
            'pt' => $value * 0.352778,
            default => $value, // mm
        };
    }

    /**
     * Convert hex color to RGB array
     */
    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $len = strlen($hex);

        if ($len === 6) {
            [$r, $g, $b] = sscanf($hex, "%02x%02x%02x");
        } elseif ($len === 3) {
            [$r, $g, $b] = sscanf($hex, "%1x%1x%1x");
            $r = $r * 17;
            $g = $g * 17;
            $b = $b * 17;
        } else {
            return [26, 26, 26]; // Default dark
        }

        return [(int)$r, (int)$g, (int)$b];
    }

    /**
     * Process images for PDF
     */
    protected function processImagesForPdf(Collection $questions): Collection
    {
        return $questions->map(function ($question) {
            if (isset($question->formattedQuestion)) {
                if (isset($question->formattedQuestion->question->images)) {
                    $question->formattedQuestion->question->images = $this->convertImagePaths(
                        $question->formattedQuestion->question->images
                    );
                }

                if (isset($question->formattedQuestion->options)) {
                    foreach ($question->formattedQuestion->options as $option) {
                        if (isset($option->images)) {
                            $option->images = $this->convertImagePaths($option->images);
                        }
                    }
                }

                if (isset($question->formattedQuestion->solution->images)) {
                    $question->formattedQuestion->solution->images = $this->convertImagePaths(
                        $question->formattedQuestion->solution->images
                    );
                }
            }

            return $question;
        });
    }

    /**
     * Convert image paths
     */
    protected function convertImagePaths(array $images): array
    {
        return array_map(function ($imagePath) {
            if (file_exists($imagePath)) {
                return $imagePath;
            }

            $storagePath = storage_path('app/public/' . ltrim($imagePath, '/'));
            if (file_exists($storagePath)) {
                return $storagePath;
            }

            $publicPath = public_path('storage/' . ltrim($imagePath, '/'));
            if (file_exists($publicPath)) {
                return $publicPath;
            }

            return null;
        }, array_filter($images));
    }

    /**
     * Process math formulas
     */
    protected function processMathFormulas(Collection $questions): Collection
    {
        return $questions->map(function ($question) {
            if (isset($question->formattedQuestion)) {
                if (isset($question->formattedQuestion->question->text)) {
                    if (isset($question->formattedQuestion->question->text->en)) {
                        $question->formattedQuestion->question->text->en =
                            $this->mathRendererService->preprocessQuestionText(
                                $question->formattedQuestion->question->text->en
                            );
                    }
                    if (isset($question->formattedQuestion->question->text->hi)) {
                        $question->formattedQuestion->question->text->hi =
                            $this->mathRendererService->preprocessQuestionText(
                                $question->formattedQuestion->question->text->hi
                            );
                    }
                }

                if (isset($question->formattedQuestion->options)) {
                    foreach ($question->formattedQuestion->options as $option) {
                        if (isset($option->text->en)) {
                            $option->text->en = $this->mathRendererService->preprocessQuestionText($option->text->en);
                        }
                        if (isset($option->text->hi)) {
                            $option->text->hi = $this->mathRendererService->preprocessQuestionText($option->text->hi);
                        }
                    }
                }

                if (isset($question->formattedQuestion->solution->text)) {
                    if (isset($question->formattedQuestion->solution->text->en)) {
                        $question->formattedQuestion->solution->text->en =
                            $this->mathRendererService->preprocessQuestionText(
                                $question->formattedQuestion->solution->text->en
                            );
                    }
                    if (isset($question->formattedQuestion->solution->text->hi)) {
                        $question->formattedQuestion->solution->text->hi =
                            $this->mathRendererService->preprocessQuestionText(
                                $question->formattedQuestion->solution->text->hi
                            );
                    }
                }
            }

            return $question;
        });
    }
}
