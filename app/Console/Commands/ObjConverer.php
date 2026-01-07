<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;

class ObjConverer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obj:convert';

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

        // Example usage
        $questions = $this->extractQuestionsFromHTML('./public/word_files/output.html');
        file_put_contents('./public/word_files/parsed_questions.json', json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
                'question' => ['text' => '', 'images' => []],
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

                // Header row → question
                if ($th->length >= 2 && $index == 0) {
                    $qCell = $th->item(1);
                    $questionData['question']['text'] = trim($qCell->textContent);

                    $imgTags = $qCell->getElementsByTagName('img');
                    foreach ($imgTags as $img) {
                        $src = $img->getAttribute('src');
                        if ($src) $questionData['question']['images'][] = $src;
                    }
                    continue;
                }

                $tds = $row->getElementsByTagName('td');
                if ($tds->length < 2) continue;

                $key = strtolower(trim($tds->item(0)->textContent));
                $valueCell = $tds->item(1);

                $valueText = trim($valueCell->textContent);
                $images = [];
                foreach ($valueCell->getElementsByTagName('img') as $img) {
                    $src = $img->getAttribute('src');
                    if ($src) $images[] = $src;
                }

                switch (true) {
                    case str_starts_with($key, 'type'):
                        $questionData['type'] = $valueText;
                        break;

                    case str_starts_with($key, 'option'):
                        $questionData['options'][] = [
                            'text' => $valueText,
                            'images' => $images
                        ];
                        break;

                    case str_starts_with($key, 'answer'):
                        $questionData['answer'] = [
                            'text' => $valueText,
                            'images' => $images
                        ];
                        break;

                    case str_starts_with($key, 'solution'):
                        $questionData['solution'] = [
                            'text' => $valueText,
                            'images' => $images
                        ];
                        break;

                    case str_starts_with($key, 'positive'):
                        $questionData['positive_marks'] = $valueText;
                        break;

                    case str_starts_with($key, 'negative'):
                        $questionData['negative_marks'] = $valueText;
                        break;
                }
            }

            $questions[] = $questionData;
        }

        return $questions;
    }

}
