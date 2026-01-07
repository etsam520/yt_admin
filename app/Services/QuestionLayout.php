<?php
namespace App\Services;
use App\Services\TBuilder;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;
use PhpParser\Node\Expr\FuncCall;

class QuestionLayout {
    private $pdf;
    private $question;
    private $questionNumber;
    private $options;
    private $language;

    public function __construct(TBuilder $pdf, $question, $questionNumber = 1, $options = [], $language = 'both') {
        $this->pdf = $pdf;
        $this->question = $question;
        $this->questionNumber = $questionNumber;
        $this->options = $options;
        $this->language = $language;
    }

    public function processQuestion() {
        $pdf = $this->pdf;
        
        // Set font for question
        $pdf->SetFont("helvetica", "", 11);
        $_QFILTER = $this->qFilter();
        
        // Prepare question text with minimal spacing
        $questionText = "Q{$this->questionNumber}.";
        
        if (isset($this->question->question_text_en)) {
            $questionText .= " " . $this->question->question_text_en;
        }
        
        // Set font with Unicode support
        $pdf->SetFont('freesans', '', $_QFILTER['font_size'], '', true);
        
        if (isset($this->question->question_text_hi)) {
            $questionText .= "\n" . $this->question->question_text_hi;
        }
        
        // // Add options if they exist
        // if (isset($this->question->options) && !empty($this->question->options)) {
        //     $questionText .= "\nOptions:\n";
        //     foreach ($this->question->options as $key => $option) {
        //         $questionText .= chr(65 + $key) . ". " . ($option->text_en ?? '') . "\n";
        //     }
        // }
        
        // Write question to PDF with enhanced styling
        $this->addStyledTextBlock($pdf, $questionText, $_QFILTER, $_QFILTER['show_border']);
        
        $pdf = $this->setOptions($pdf);

        $pdf = $this->setSolution($pdf);
        
        return $pdf;
    }

    private function setOptions($pdf)
    {
        if (empty($this->question->formattedQuestion->options)) {
            return $pdf;
        }
        $_QOFILTER = $this->qoFilter();

        // Use a compact text style
        $pdf->SetFont("freesans", "", $_QOFILTER['font_size']);
       

        foreach ($this->question->formattedQuestion->options as $index => $option) {

            $label = chr(65 + $index);  // A, B, C, D...

            $en = $option->text->en ?? "";
            $hi = $option->text->hi ?? "";

            // Make a compact 2-line option
            // EN on first line, Hindi below it (if exists)
            if ($hi !== "") {
                $text = "$label) $en\n     $hi";
            } else {
                $text = "$label) $en";
            }
            $pdf->setTextColor(...$_QOFILTER['color']);
            $pdf->setFillColor(...$_QOFILTER['background_color']);
            // Option with enhanced styling
            $this->addStyledTextBlock($pdf, $text, $_QOFILTER, $_QOFILTER['show_border']);
            
            // Add minimal spacing between options
            if ($index < count($this->question->formattedQuestion->options) - 1) {
                $optionSpacing = $_QOFILTER['spacing_after'] ?? 0.2;
                $pdf->Ln($optionSpacing);
            }
        }

        return $pdf;
    }

    private function setSolution($pdf){
        // Check if solution exists and should be shown
        if (empty($this->question->formattedQuestion->solution) || 
            !($this->options['show_solutions'] ?? true)) {
            return $pdf;
        }

        $_QSFILTER = $this->qsFilter();
        
        // Set font for solution with Unicode support
        $pdf->SetFont('freesans', '', $_QSFILTER['font_size'], '', true);
        
        $solution = $this->question->formattedQuestion->solution;
        $hi = $solution->text->hi ?? '';
        $en = $solution->text->en ?? '';
        
        // Minimal space before solution
        $spacingBeforeSolution = $_QSFILTER['spacing_before'] ?? 1;
        $pdf->Ln($spacingBeforeSolution);
        
        // Apply custom colors and styling
        $pdf->setTextColor(...$_QSFILTER['color']);
        $pdf->setFillColor(...$_QSFILTER['background_color']);
        
        // Prepare solution text with compact header
        $solutionHeader = "💡 Solution:";
        $solutionContent = "";
        
        if ($this->language === 'english' || $this->language === 'both') {
            if (!empty($en)) {
                $solutionContent .= $en;
                if ($this->language === 'both' && !empty($hi)) {
                    $solutionContent .= "\n";
                }
            }
        }
        
        if ($this->language === 'hindi' || $this->language === 'both') {
            if (!empty($hi)) {
                $solutionContent .= $hi;
            }
        }
        
        // Add solution as single block with header and content
        $fullSolutionText = $solutionHeader;
        if (!empty($solutionContent)) {
            $fullSolutionText .= "\n" . $solutionContent;
        }
        
        $pdf->SetFont('freesans', '', $_QSFILTER['font_size'], '', true);
        $this->addStyledTextBlock($pdf, $fullSolutionText, $_QSFILTER, $_QSFILTER['show_border']);
        
        return $pdf;
    }


    private function qFilter() :array
    {
        return [
            'color' => $this->hexToRgb($this->options['question']['color'] ?? '#2c3e50'),
            'background_color' => $this->hexToRgb($this->options['question']['background_color'] ?? '#ffffff'),
            'font_size' => $this->validateFontSize($this->options['question']['font_size'] ?? 11, 11),
            'show_border' => $this->options['question']['show_border'] ?? false,
            'auto_contrast' => $this->options['question']['auto_contrast'] ?? true,
            'padding' => $this->validateSpacing($this->options['question']['padding'] ?? 1, 'padding'),
            'line_height' => $this->validateSpacing($this->options['question']['line_height'] ?? 5, 'line_height'),
            'spacing_after' => $this->validateSpacing($this->options['question']['spacing_after'] ?? 1, 'spacing_after'),
            'language' => $this->language ?? 'both'
        ];
    }

    private function qoFilter() :array
    {
        return [
            'color' => $this->hexToRgb($this->options['question_options']['color'] ?? '#495057'),
            'background_color' => $this->hexToRgb($this->options['question_options']['background_color'] ?? '#f8f9fa'),
            'font_size' => $this->validateFontSize($this->options['question_options']['font_size'] ?? 10, 10),
            'show_border' => $this->options['question_options']['show_border'] ?? false,
            'auto_contrast' => $this->options['question_options']['auto_contrast'] ?? true,
            'padding' => $this->validateSpacing($this->options['question_options']['padding'] ?? 0.5, 'padding'),
            'line_height' => $this->validateSpacing($this->options['question_options']['line_height'] ?? 4, 'line_height'),
            'spacing_after' => $this->validateSpacing($this->options['question_options']['spacing_after'] ?? 0.5, 'spacing_after'),
        ];
    }
    private function qsFilter() :array
    {
        return [
            'color' => $this->hexToRgb($this->options['question_solution']['color'] ?? '#dc3545'),
            'background_color' => $this->hexToRgb($this->options['question_solution']['background_color'] ?? '#fff3cd'),
            'font_size' => $this->validateFontSize($this->options['question_solution']['font_size'] ?? 10, 10),
            'show_border' => $this->options['question_solution']['show_border'] ?? false,
            'auto_contrast' => $this->options['question_solution']['auto_contrast'] ?? true,
            'padding' => $this->validateSpacing($this->options['question_solution']['padding'] ?? 0.5, 'padding'),
            'line_height' => $this->validateSpacing($this->options['question_solution']['line_height'] ?? 4, 'line_height'),
            'spacing_before' => $this->validateSpacing($this->options['question_solution']['spacing_before'] ?? 1, 'spacing_before'),
        ];
    }

    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } elseif (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            // Return default dark color for invalid hex
            return [44, 62, 80]; // Default dark blue-gray
        }

        return [$r, $g, $b];
    }

    /**
     * Validate and sanitize font size
     */
    private function validateFontSize($size, $default = 11, $min = 9, $max = 20)
    {
        $size = is_numeric($size) ? (float)$size : $default;
        // Ensure font size is reasonable for PDF rendering
        return max($min, min($max, $size));
    }

    /**
     * Validate spacing values to prevent PDF rendering issues
     */
    private function validateSpacing($value, $type = 'general')
    {
        if (!is_numeric($value)) {
            return $this->getDefaultSpacing($type);
        }
        
        $value = (float)$value;
        
        return match($type) {
            'line_height' => max(3.5, min(12, $value)), // 3.5mm - 12mm
            'padding' => max(0, min(5, $value)),         // 0mm - 5mm  
            'spacing_after' => max(0.2, min(8, $value)), // 0.2mm - 8mm
            'spacing_before' => max(0.5, min(10, $value)), // 0.5mm - 10mm
            default => max(0.5, min(10, $value))         // General spacing
        };
    }

    /**
     * Get default spacing values by type
     */
    private function getDefaultSpacing($type)
    {
        return match($type) {
            'line_height' => 4.5,
            'padding' => 1,
            'spacing_after' => 1,
            'spacing_before' => 1,
            default => 2
        };
    }

    /**
     * Calculate color brightness (0-255)
     */
    private function getBrightness($rgb)
    {
        return ($rgb[0] * 299 + $rgb[1] * 587 + $rgb[2] * 114) / 1000;
    }

    /**
     * Get contrasting text color based on background
     */
    private function getContrastingColor($backgroundColor)
    {
        $brightness = $this->getBrightness($backgroundColor);
        // If background is light, use dark text; if dark, use light text
        return $brightness > 128 ? [0, 0, 0] : [255, 255, 255];
    }

    /**
     * Add a text block with optional border and enhanced styling
     */
    private function addStyledTextBlock($pdf, $text, $config, $addBorder = false)
    {
        // Get current column width (respects multi-column layout)
        $availableWidth = $this->getAvailableColumnWidth($pdf);
        
        // Prepare text for better wrapping
        $wrappedText = $this->prepareTextForWrapping($text);
        
        // Set background color
        $pdf->setFillColor(...$config['background_color']);
        
        // Auto-adjust text color for better contrast if needed
        $useAutoContrast = $config['auto_contrast'] ?? true;
        if ($useAutoContrast) {
            $contrastColor = $this->getContrastingColor($config['background_color']);
            $pdf->setTextColor(...$contrastColor);
        } else {
            $pdf->setTextColor(...$config['color']);
        }
        
        // Use validated spacing values
        $lineHeight = $this->validateSpacing($config['line_height'] ?? 4.5, 'line_height');
        $padding = $this->validateSpacing($config['padding'] ?? 1, 'padding');
        
        if ($addBorder) {
            $pdf->SetDrawColor(128, 128, 128); // Light gray border
            // Use MultiCell with border for proper text wrapping
            $pdf->MultiCell($availableWidth, $lineHeight, $wrappedText, 1, 'L', true, 1, '', '', true, 0, false, true, 0, 'T');
        } else {
            // Use MultiCell with fill for proper background rendering and text wrapping
            $pdf->MultiCell($availableWidth, $lineHeight, $wrappedText, 0, 'L', true, 1, '', '', true, 0, false, true, 0, 'T');
        }
        
        // Add padding only if explicitly set
        if ($padding > 0) {
            $pdf->Ln($padding);
        }
        
        // Reset colors
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->setTextColor(0, 0, 0);
    }

    /**
     * Get available column width respecting multi-column layout
     */
    private function getAvailableColumnWidth($pdf)
    {
        // Based on TcpdfGeneratorService: setEqualColumns(2, 90)
        // This means 2 columns, each 90mm wide
        // However, we need to be conservative to prevent overflow
        
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        
        // For A4 portrait: 210mm page width, ~15mm margins each side
        // Available width: ~180mm, so 90mm per column is correct
        // But we'll use 85mm to be safe and account for padding
        
        if ($pageWidth > 150) { // A4 or larger
            return 82; // Conservative column width for 2-column layout
        } else {
            // Smaller page size
            $totalWidth = $pageWidth - $margins['left'] - $margins['right'];
            return min($totalWidth * 0.9, 120);
        }
    }

    /**
     * Prepare text for better wrapping by breaking long words
     */
    private function prepareTextForWrapping($text, $maxWidth = 30)
    {
        $words = explode(' ', $text);
        $processedWords = [];
        
        foreach ($words as $word) {
            if (strlen($word) > $maxWidth) {
                // Break long words into smaller chunks
                $chunks = str_split($word, $maxWidth - 1);
                $processedWords = array_merge($processedWords, $chunks);
            } else {
                $processedWords[] = $word;
            }
        }
        
        return implode(' ', $processedWords);
    }   


}