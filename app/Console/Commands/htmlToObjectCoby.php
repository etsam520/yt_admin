<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;

class htmlToObjectCoby extends Command
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
       $html = file_get_contents('./public/word_files/output.html');

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
                'answer' => '',
                'solution' => '',
                'positive_marks' => '',
                'negative_marks' => ''
            ];

            $rows = $table->getElementsByTagName('tr');
            foreach ($rows as $index => $row) {
                
                $cells = $row->getElementsByTagName('td');
                $cellCount = $cells->length;

                if ($index === 0) {
                    // First row (header): extract question
                    $ths = $row->getElementsByTagName('th');
                    if ($ths->length >= 2) {
                        $questionData['question'] = trim($ths->item(1)->textContent);
                    }
                } elseif ($cellCount >= 2) {
                    $key = strtolower(trim($cells->item(0)->textContent));
                    $value = trim($cells->item(1)->textContent);

                    switch (true) {
                        case str_starts_with($key, 'type'):
                            $questionData['type'] = $value;
                            break;
                        case str_starts_with($key, 'option'):
                            $questionData['options'][] = $value;
                            break;
                        case str_starts_with($key, 'answer'):
                            $questionData['answer'] = $value;
                            break;
                        case str_starts_with($key, 'solution'):
                            $questionData['solution'] = $value;
                            break;
                        case str_starts_with($key, 'positive'):
                            $questionData['positive_marks'] = $value;
                            break;
                        case str_starts_with($key, 'negative'):
                            $questionData['negative_marks'] = $value;
                            break;
                    }
                }
            }

            $questions[] = $questionData;
        }

        // Save to JSON
        file_put_contents('./public/word_files/parsed_questions.json', json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo "Extracted " . count($questions) . " questions.\n";

    }


}
