<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExtractQuestionsFromDocx extends Command
{
    protected $signature = 'extract:questions {jsonFile}';
    protected $description = 'Extract complex questions with LaTeX, superscripts, and images from Pandoc JSON';

    public function handle()
    {
        $filePath = $this->argument('jsonFile');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $json = json_decode(file_get_contents($filePath), true);
        $blocks = $json['blocks'] ?? [];

        $questions = [];

        foreach ($blocks as $block) {
            if ($block['t'] !== 'Table') continue;

            $rows = $block['c'][4] ?? [];
            $questionData = [
                'question' => '',
                'options' => [],
                'answer' => '',
                'solution' => '',
                'positive marks' => '',
                'negative marks' => '',
                'type' => ''
            ];

            foreach ($rows as $row) {
                if (!isset($row[1]) || !is_array($row[1])) continue;
                $cells = $row[1];
                if (count($cells) < 2) continue;

                $keyRaw = $cells[0]['c'] ?? [];
                $valRaw = $cells[1]['c'] ?? [];

                $key = strtolower(trim($this->extractTextFromCell($keyRaw)));
                $value = trim($this->extractTextFromCell($valRaw));

                Log::debug("Key: $key | Value: $value");

                // Match key to known patterns
                switch (true) {
                    case str_contains($key, 'question'):
                        $questionData['question'] = $value;
                        break;
                    case str_contains($key, 'type'):
                        $questionData['type'] = $value;
                        break;
                    case str_contains($key, 'option'):
                        if (!empty($value)) $questionData['options'][] = $value;
                        break;
                    case str_contains($key, 'answer'):
                        $questionData['answer'] = $value;
                        break;
                    case str_contains($key, 'solution'):
                        $questionData['solution'] = $value;
                        break;
                    case str_contains($key, 'positive'):
                        $questionData['positive marks'] = $value;
                        break;
                    case str_contains($key, 'negative'):
                        $questionData['negative marks'] = $value;
                        break;
                }
            }

            if (!empty($questionData['question']) && count($questionData['options']) > 0) {
                $questions[] = $questionData;
            }
        }

        $outputPath = storage_path('app/question_output.json');
        file_put_contents($outputPath, json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Extracted questions saved to: $outputPath");
        return 0;
    }


    private function extractTextFromCell(array $cellBlocks): string
    {
        $text = '';
        foreach ($cellBlocks as $block) {
            if (isset($block['t']) && isset($block['c'])) {
                switch ($block['t']) {
                    case 'Para':
                    case 'Plain':
                        $text .= $this->parseInlines($block['c']) . "\n";
                        break;
                    case 'Table':
                        $text .= '[table]\n';
                        break;
                    default:
                        if (is_array($block['c'])) {
                            $text .= $this->extractTextFromCell($block['c']);
                        }
                        break;
                }
            }
        }
        return trim($text);
    }

    private function parseInlines(array $inlines): string
    {
        $text = '';
        foreach ($inlines as $el) {
            switch ($el['t']) {
                case 'Str':
                    $text .= $el['c'];
                    break;
                case 'Space':
                    $text .= ' ';
                    break;
                case 'SoftBreak':
                    $text .= "\n";
                    break;
                case 'Math':
                    $text .= '$' . $el['c'][1] . '$';
                    break;
                case 'Superscript':
                    $text .= '<sup>' . $this->parseInlines($el['c']) . '</sup>';
                    break;
                case 'Image':
                    $altText = $el['c'][0][0]['c'] ?? 'image';
                    $src = $el['c'][1][0] ?? 'missing.png';
                    $text .= "<img alt=\"$altText\" src=\"$src\" />";
                    break;
                case 'Strong':
                case 'Emph':
                case 'Span':
                    $text .= $this->parseInlines($el['c']);
                    break;
            }
        }
        return $text;
    }
}
