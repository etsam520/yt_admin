<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;

class htmlToObjectCopy2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'htiml:object';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Example usage => with images
        $questions = $this->extractQuestionsFromHTML('./public/word_files/output.html');
        file_put_contents('./public/word_files/parsed_questions.json', json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "Extracted " . count($questions) . " questions.\n";
    }

        function extractQuestionsFromHTML($htmlFilePath) {
            $html = file_get_contents($htmlFilePath);
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $tables = $xpath->query('//table');
            $questions = [];

            foreach ($tables as $table) {
                $questionData = [
                    'question' => '',
                    'type' => '',
                    'options' => [],
                    'answer' => ['text' => '', 'images' => []],
                    'solution' => ['text' => '', 'images' => []],
                    'positive_marks' => '',
                    'negative_marks' => ''
                ];

                $rows = $table->getElementsByTagName('tr');

                foreach ($rows as $index => $row) {
                    $th = $row->getElementsByTagName('th');
                    if ($th->length >= 2 && $index == 0) {
                        $questionData['question'] = trim($th->item(1)->textContent);
                        continue;
                    }

                    $tds = $row->getElementsByTagName('td');
                    if ($tds->length < 2) continue;

                    $key = strtolower(trim($tds->item(0)->textContent));
                    $valueCell = $tds->item(1);

                    // Extract images
                    $images = [];
                    foreach ($valueCell->getElementsByTagName('img') as $img) {
                        $src = $img->getAttribute('src');
                        if ($src) $images[] = $src;
                    }

                    // Extract text (including inline LaTeX)
                    $value = trim($dom->saveHTML($valueCell));

                    switch (true) {
                        case str_starts_with($key, 'type'):
                            $questionData['type'] = strip_tags($value);
                            break;
                        case str_starts_with($key, 'option'):
                            $questionData['options'][] = [
                                'text' => strip_tags($value),
                                'images' => $images
                            ];
                            break;
                        case str_starts_with($key, 'answer'):
                            $questionData['answer'] = [
                                'text' => strip_tags($value),
                                'images' => $images
                            ];
                            break;
                        case str_starts_with($key, 'solution'):
                            $questionData['solution'] = [
                                'text' => strip_tags($value),
                                'images' => $images
                            ];
                            break;
                        case str_starts_with($key, 'positive'):
                            $questionData['positive_marks'] = strip_tags($value);
                            break;
                        case str_starts_with($key, 'negative'):
                            $questionData['negative_marks'] = strip_tags($value);
                            break;
                    }
                }

                $questions[] = $questionData;
            }

            return $questions;
        }

     
            
      
    
}

/*
{
  "question": "The hybridization of the central carbon in CH3C≡N and the bond angle CCN are",
  "type": "multiple_choice",
  "options": [
    {
      "text": "sp² , 180°",
      "images": []
    },
    {
      "text": "sp³ , 109°.$\\lim_{n \\rightarrow \\infty}\\left( 1 + \\frac{1}{n} \\right)^n$",
      "images": []
    },
    {
      "text": "option with image",
      "images": ["media/image1.emf", "media/image2.png"]
    }
  ],
  "answer": {
    "text": "1,2 $...$",
    "images": []
  },
  "solution": {
    "text": "Sp, 180°",
    "images": ["media/image3.emf"]
  },
  "positive_marks": "4",
  "negative_marks": "1"
}
*/
