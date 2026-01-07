<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class MathRendererService
{
    protected $nodeScriptPath;
    protected $tempPath;

    public function __construct()
    {
        $this->nodeScriptPath = resource_path('js/math-renderer.js');
        $this->tempPath = storage_path('app/temp/math/');
        
        // Ensure temp directory exists
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
    }

    /**
     * Process HTML content to render mathematical formulas
     * @param string $htmlContent
     * @return string Processed HTML with rendered math
     */
    public function renderMathInHTML(string $htmlContent): string
    {
        try {
            // Check if Node.js is available
            if (!$this->isNodeAvailable()) {
                Log::warning('Node.js not available, returning HTML without math rendering');
                return $this->fallbackMathRendering($htmlContent);
            }

            // Create temporary input file
            $inputFile = $this->tempPath . uniqid('math_input_') . '.html';
            $outputFile = $this->tempPath . uniqid('math_output_') . '.html';
            
            file_put_contents($inputFile, $htmlContent);

            // Run Node.js script to render math
            $process = new Process([
                'node',
                $this->nodeScriptPath,
                $inputFile,
                $outputFile
            ]);
            
            $process->setTimeout(30);
            $process->run();

            if ($process->isSuccessful() && file_exists($outputFile)) {
                $renderedContent = file_get_contents($outputFile);
                
                // Cleanup
                unlink($inputFile);
                unlink($outputFile);
                
                return $renderedContent;
            } else {
                Log::error('Math rendering failed: ' . $process->getErrorOutput());
                unlink($inputFile);
                return $this->fallbackMathRendering($htmlContent);
            }

        } catch (\Exception $e) {
            Log::error('Math rendering error: ' . $e->getMessage());
            return $this->fallbackMathRendering($htmlContent);
        }
    }

    /**
     * Check if Node.js is available
     * @return bool
     */
    protected function isNodeAvailable(): bool
    {
        $process = new Process(['node', '--version']);
        $process->run();
        
        return $process->isSuccessful();
    }

    /**
     * Fallback method: Add CSS for basic math rendering
     * @param string $htmlContent
     * @return string
     */
    protected function fallbackMathRendering(string $htmlContent): string
    {
        // Replace LaTeX delimiters with styled spans for basic rendering
        
        // Display math $$...$$
        $htmlContent = preg_replace_callback(
            '/\$\$(.+?)\$\$/s',
            function ($matches) {
                return '<div class="math-display" style="text-align: center; margin: 12px 0; font-family: \'Latin Modern Math\', \'Cambria Math\', serif;">' 
                    . htmlspecialchars($matches[1]) 
                    . '</div>';
            },
            $htmlContent
        );

        // Inline math $...$
        $htmlContent = preg_replace_callback(
            '/\$(.+?)\$/s',
            function ($matches) {
                return '<span class="math-inline" style="font-family: \'Latin Modern Math\', \'Cambria Math\', serif; font-style: italic;">' 
                    . htmlspecialchars($matches[1]) 
                    . '</span>';
            },
            $htmlContent
        );

        return $htmlContent;
    }

    /**
     * Convert common LaTeX symbols to Unicode
     * @param string $latex
     * @return string
     */
    protected function convertLatexToUnicode(string $latex): string
    {
        $replacements = [
            '\\alpha' => 'α',
            '\\beta' => 'β',
            '\\gamma' => 'γ',
            '\\delta' => 'δ',
            '\\epsilon' => 'ε',
            '\\theta' => 'θ',
            '\\lambda' => 'λ',
            '\\mu' => 'μ',
            '\\pi' => 'π',
            '\\sigma' => 'σ',
            '\\phi' => 'φ',
            '\\omega' => 'ω',
            '\\Delta' => 'Δ',
            '\\Gamma' => 'Γ',
            '\\Lambda' => 'Λ',
            '\\Omega' => 'Ω',
            '\\Pi' => 'Π',
            '\\Sigma' => 'Σ',
            '\\Phi' => 'Φ',
            '\\infty' => '∞',
            '\\pm' => '±',
            '\\times' => '×',
            '\\div' => '÷',
            '\\leq' => '≤',
            '\\geq' => '≥',
            '\\neq' => '≠',
            '\\approx' => '≈',
            '\\equiv' => '≡',
            '\\subset' => '⊂',
            '\\supset' => '⊃',
            '\\in' => '∈',
            '\\notin' => '∉',
            '\\cup' => '∪',
            '\\cap' => '∩',
            '\\sqrt' => '√',
            '\\sum' => '∑',
            '\\prod' => '∏',
            '\\int' => '∫',
            '\\partial' => '∂',
            '\\nabla' => '∇',
            '\\rightarrow' => '→',
            '\\leftarrow' => '←',
            '\\Rightarrow' => '⇒',
            '\\Leftarrow' => '⇐',
            '\\leftrightarrow' => '↔',
            '\\Leftrightarrow' => '⇔',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $latex);
    }

    /**
     * Pre-process question text to ensure proper math rendering
     * @param string $text
     * @return string
     */
    public function preprocessQuestionText(string $text): string
    {
        // Ensure proper spacing around math delimiters
        $text = preg_replace('/([^\s])\$/', '$1 $', $text);
        $text = preg_replace('/\$([^\s])/', '$ $1', $text);
        
        return $text;
    }
}
