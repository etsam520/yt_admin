<?php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class QuestionParser
{
    protected string $filePath;
    private $tlog ;

    public function parse(string $rawText): array
    {
        $questions = [];
        $logFile = storage_path('logs/block_output.log');
        
        // Create log file and directory if they don't exist
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        if (!file_exists($logFile)) {
            touch($logFile);
        }
        
        $this->tlog = $logFile;
        Log::info('Parsing question text', ['rawText' => $rawText]);
        if(gettype($rawText) !== 'string' || $rawText == ''){
            throw new \Exception('Cannot Process');
        }

        // Split text into question blocks starting from "Question English"
        $blocks = $this->extractQuestionBlocks($rawText);

        // file_put_contents($logFile, "Total blocks found: " . count($blocks) . "\n\n", FILE_APPEND);
        
        foreach ($blocks as $index => $block) {
            // file_put_contents($logFile, "Processing Block $index: \n" . $block . "\n\n", FILE_APPEND);
            
            $question = $this->parseQuestionBlock($block);
            if ($question && !empty($question['question']['text']['en'])) {
                $questions[] = $question;
            }
        }
        
        // file_put_contents($logFile, "Parsed questions: " . json_encode($questions, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
        
        return $questions;
    }


     /**
     * Extract question blocks from raw text
     */
    protected function extractQuestionBlocks(string $rawText): array
    {
        $blocks = [];
        $lines = explode("\n", $rawText);
        $currentBlock = '';
        $inQuestionBlock = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Check if line starts a new question block
            if (stripos($line, 'Question English') !== false) {
                // Save previous block if exists
                if ($inQuestionBlock && !empty(trim($currentBlock))) {
                    $blocks[] = trim($currentBlock);
                }
                
                // Start new block
                $currentBlock = $line . "\n";
                $inQuestionBlock = true;
                continue;
            }
            
            // If we're in a question block, add the line
            if ($inQuestionBlock) {
                $currentBlock .= $line . "\n";
                
                // Check if this is the end of the block (Specialization line)
                if (stripos($line, 'Specialization') !== false) {
                    $blocks[] = trim($currentBlock);
                    $currentBlock = '';
                    $inQuestionBlock = false;
                }
            }
        }
        
        // Add last block if exists
        if ($inQuestionBlock && !empty(trim($currentBlock))) {
            $blocks[] = trim($currentBlock);
        }
        
        return $blocks;
    }

    /**
     * Parse a single question block
     */
    protected function parseQuestionBlock(string $block): ?array
    {
        $question = $this->emptyQuestion();
        $lines = explode("\n", $block);
        
        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) continue;
                // file_put_contents($this->tlog, $line. "\n\n", FILE_APPEND);

            // Parse key-value pairs from table format
            if (preg_match('/^([A-Za-z ]+?)\s{2,}(.+)$/m', $line, $matches)) {
                // dd($matches);
                
                $field = trim($matches[1]);
                $value = trim($matches[2]);
                file_put_contents($this->tlog, "key : ".$matches[1]." value : ".$matches[2]. "\n\n", FILE_APPEND);

                if (!empty($field) && !empty($value)) {
                    $this->mapField($question, $field, $value);
                }
            } else {
                // Handle lines that might not be in perfect table format
                $parts = explode('|', $line);
                if (count($parts) >= 3) {
                    $field = trim($parts[1] ?? '');
                    $value = trim($parts[2] ?? '');
                    
                    if (!empty($field) && !empty($value)) {
                        $this->mapField($question, $field, $value);
                    }
                }
            }
        }
        
        return $this->finalize($question);
    }

    /**
     * Create empty question skeleton
     */
    protected function emptyQuestion(): array
    {
        return [
            'key' => '',
            'question' => [
                'text' => ['en' => '', 'hi' => ''],
                'images' => []
            ],
            'type' => '',
            'options' => [],
            'answer' => '',
            'solution' => [
                'text' => ['en' => '', 'hi' => ''],
                'images' => []
            ],
            'specialization' => [],
        ];
    }

    /**
     * Map a single field/value to the question array
     */
    protected function mapField(array &$current, string $field, string $value): void
    {
        // Extract images
        $images = $this->imageExtraction($value);
        
        // Clean value
        $cleanValue = $this->cleanValue($value);
        
        if (empty($cleanValue)) {
            return;
        }
        
        $field = strtolower(trim($field));
        
        switch (true) {
            case stripos($field, 'question english') !== false:
                $current['question']['text']['en'] = $cleanValue;
                $current['question']['images'] = array_merge($current['question']['images'], $images);
                break;
                
            case stripos($field, 'question hindi') !== false:
                $current['question']['text']['hi'] = $cleanValue;
                $current['question']['images'] = array_merge($current['question']['images'], $images);
                break;
                
            case stripos($field, 'type') !== false:
                $current['type'] = $cleanValue;
                break;
                
            case stripos($field, 'option english') !== false:
                $current['options'][] = ['lang' => 'en', 'text' => $cleanValue, 'images' => $images];
                break;
                
            case stripos($field, 'option hindi') !== false:
                $current['options'][] = ['lang' => 'hi', 'text' => $cleanValue, 'images' => $images];
                break;
                
            case stripos($field, 'answer') !== false:
                $current['answer'] = $cleanValue;
                break;
                
            case stripos($field, 'solution english') !== false:
                $current['solution']['text']['en'] = $cleanValue;
                $current['solution']['images'] = array_merge($current['solution']['images'], $images);
                break;
                
            case stripos($field, 'solution hindi') !== false:
                $current['solution']['text']['hi'] = $cleanValue;
                $current['solution']['images'] = array_merge($current['solution']['images'], $images);
                break;
                
            case stripos($field, 'specialization') !== false:
                $specializations = array_filter(array_map('trim', preg_split('/[,&]/', $cleanValue)));
                $current['specialization'] = $specializations;
                break;
        }
    }

    /**
     * Clean value by removing unwanted characters and formatting
     */
    protected function cleanValue(string $value): string
    {
        // Remove image markdown
        $cleanValue = preg_replace('/!\[[^\]]*\]\([^)]+\)/', '', $value);
        
        // Remove LaTeX delimiters
        // $cleanValue = preg_replace('/\${1,2}(.*?)\${1,2}/', '$1', $cleanValue);
        
        // Remove markdown headers
        $cleanValue = preg_replace('/^#+\s*/', '', $cleanValue);
        
        // Remove image placeholders
        $cleanValue = preg_replace('/\[\[([\s\S]*?)\]\]/i', ' ', $cleanValue);
        
        // Normalize whitespace
        $cleanValue = preg_replace('/\s+/', ' ', $cleanValue);
        
        return trim($cleanValue);
    }

    /**
     * Final cleanup before returning a question
     */
    protected function finalize(array $q): array
    {
        // Trim extra spaces
        $q['question']['text']['en'] = trim($q['question']['text']['en']);
        $q['question']['text']['hi'] = trim($q['question']['text']['hi']);
        $q['solution']['text']['en'] = trim($q['solution']['text']['en']);
        $q['solution']['text']['hi'] = trim($q['solution']['text']['hi']);
        
        // Generate key if not exists
        if (empty($q['key']) && !empty($q['question']['text']['en'])) {
            $q['key'] = 'Q_' . substr(md5($q['question']['text']['en']), 0, 8);
        }
        
        return $q;
    }

    /**
     * Extracts all [[IMAGE:...]] placeholders from a string
     */
    private function imageExtraction(string $text): array
    {
        $images = [];
        
        // Find all [[...]] blocks
        if (preg_match_all('/\[\[([\s\S]*?)\]\]/i', $text, $blocks)) {
            foreach ($blocks[1] as $block) {
                // Extract IMAGE path
                if (preg_match('/I\s*M\s*A\s*G\s*E\s*:\s*([\s\S]+)/i', $block, $m)) {
                    $cleanPath = preg_replace('/\s+/', '', $m[1]);
                    $images[] = $cleanPath;
                }
            }
        }
        
        return $images;
    }
}
