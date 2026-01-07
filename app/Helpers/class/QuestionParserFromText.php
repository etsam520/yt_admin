<?php

namespace App\Helpers;

class QuestionParserFromText
{
    protected string $filePath;
    protected array $questions = [];

   public function parse(string $rawText): array
    {
        $logFIle = storage_path('logs/block_output.log');
        $questions = [];
        $blocks = preg_split('/\+[-]+\+/', $rawText); // split by table borders

        file_put_contents($logFIle, "row text Block: \n" . $rawText . "\n\n", FILE_APPEND);

        $current = null;

        foreach ($blocks as $block) {
            // Detect start of new question
            // file_put_contents($logFIle, "Processing Block: \n" . $block . "\n\n", FILE_APPEND);


            if (preg_match('/Question\s*:\s*(\d+)/i', $block, $m)) {
                if ($current) {
                    $questions[] = $this->finalize($current); // save previous
                }
                $current = $this->emptyQuestion();
                // continue;
            }

            if (!$current) {
                continue;
            }

            // Extract field and value from each row
            // if (preg_match('/^\|\s*([^|]+?)\s*\|\s*(.*?)\s*\|/s', trim($block), $m)) {
            //     $field = trim($m[1]);
            //     $value = trim($m[2]);

            //     // file_put_contents($logFIle, "Field: " . $field . "\nValue: " . $value . "\n\n", FILE_APPEND);

            //     $this->mapField($current, $field, $value);
            // }
            $filterdData = ["key" => "", "value" => ""] ;
            $lines = preg_split('/\n\|/', $block) ;  
            // dd($lines);
            for($i=0; $i<count($lines); $i++) {
                $line = $lines[$i];
                 if (preg_match('/^\|\s*([^|]+?)\s*\|\s*(.*?)\s*\|/s', '|' . $line, $m)) {

                    if(preg_match('/Question\s*:\s*(\d+)/i', $m[1])) {
                        $current['key'] = trim($m[1]);
                        continue;
                    }

                    $filterdData["key"] .= ' '. trim($m[1]);
                    $filterdData["value"] .=  ' '.trim($m[2]);
                }

            }


            // file_put_contents($logFIle, "Lines: " . print_r($lines) . "\n\n", FILE_APPEND);
            // foreach (preg_split('/\n\|/', $block) as $line) {

            //     $line = trim($line);
            //     // file_put_contents($logFIle, "Line: " . $line . "\n\n", FILE_APPEND);
            //     // if (empty($line)) continue;
            //     if (preg_match('/^\|\s*([^|]+?)\s*\|\s*(.*?)\s*\|/s', '|' . $line, $m)) {
            //         $filterdData["key"] .= $m[1];  //trim($m[1]);
            //         $filterdData["value"] .= $m[2]; //trim($m[2]); = trim($m[2]);
            //     }
            // }


            $field = trim($filterdData["key"]);
            $value = trim($filterdData["value"]);
            if (($field == '' || $field == null ) && ($value == '' || $value == null)) {
                continue;
            }
            $this->mapField($current, $field, $value, $logFIle);

            // file_put_contents($logFIle, "Field: " . $field . "\nValue: " . $value . "\n\n", FILE_APPEND);
        }

        // if ($current && ($current['key'] != '' && $current['key'] != null)) {
        //     dd($current);
        //     $questions[] = $this->finalize($current); // push last one
        // }

        return $questions;
    }

    /**
     * Create empty question skeleton
     */
    protected function emptyQuestion(): array
    {
        return [
            'key'    => '',
            'question' => [
                'text'   => ['en' => '', 'hi' => ''],
                'images' => []
            ],
            'type'   => '',
            'options'=> [],
            'answer' => '',
            'solution' => [
                'text'   => ['en' => '', 'hi' => ''],
                'images' => []
            ],
            'specialization' => [],
        ];
    }

    /**
     * Map a single field/value to the question array
     */
    protected function mapField(array &$current, string $field, string $value, $logFIle=null): void
    {
        // Extract images  regex : \[\[\s*IMAGE\s*:\s*([^\]\r\n]+?)\s*\]\]
        // imporoved images regex : 
        // preg_match_all('/!\[[^\]]*\]\(([^)]+)\)/', $value, $imgMatches);
        $images = $this->imageExtraction($value);
        // $images = $imgMatches[1] ?? [];
        // file_put_contents($logFIle, "key : " . $field . "\nValue: " . $value . "\n\n", FILE_APPEND);
        // foreach ($images as &$img) {
        //     $img = trim($img);
        //     file_put_contents($logFIle, "Image: " . $img . "\n\n", FILE_APPEND);


        // }

        // Clean value (remove image markdown + LaTeX delimiters + hashes)
        $cleanValue = preg_replace('/!\[[^\]]*\]\([^)]+\)/', '', $value);
        // $cleanValue = preg_replace('/\${1,2}(.*?)\${1,2}/', '$1', $cleanValue); // strip $..$ math delimiters
        $cleanValue = preg_replace('/^#+\s*/', '', $cleanValue); // strip markdown headers
        $cleanValue = trim($cleanValue);
        $cleanValue  = preg_replace('/\[\[([\s\S]*?)\]\]/i', ' ', $cleanValue); // remove images 
        $cleanValue = preg_replace('/\s+/', ' ', $cleanValue); // normalize whitespace

        // if($cleanValue == '' || $cleanValue == null) {
        //     file_put_contents($logFIle, "CleanValue: " . $cleanValue . "\n\n", FILE_APPEND);
        //     file_put_contents($logFIle, "Field: " . $field . "\nValue: " . $value . "\n\n", FILE_APPEND);    
        //     return ;
            
        // }

        // file_put_contents($logFIle, "CleanValue: " . $cleanValue . "\n\n", FILE_APPEND);

        switch (true) {
            case stripos($field, 'Question English') === 0:
                $current['question']['text']['en'] .= " " . $cleanValue;
                $current['question']['images'] = array_merge($current['question']['images'], $images);
                break;

            case stripos($field, 'Question Hindi') === 0:
                $current['question']['text']['hi'] .= " " . $cleanValue;
                $current['question']['images'] = array_merge($current['question']['images'], $images);
                break;

            case stripos($field, 'Type') === 0:
                $current['type'] = $cleanValue;
                break;

            case stripos($field, 'Option English') === 0:
                $current['options'][] = ['lang' => 'en', 'text' => $cleanValue, 'images' => $images];
                break;

            case stripos($field, 'Option Hindi') === 0:
                $current['options'][] = ['lang' => 'hi', 'text' => $cleanValue, 'images' => $images];
                break;

            case stripos($field, 'Answer') === 0:
                $current['answer'] = $cleanValue;
                break;

            case stripos($field, 'Solution English') === 0:
                $current['solution']['text']['en'] .= " " . $cleanValue;
                $current['solution']['images'] = array_merge($current['solution']['images'], $images);
                break;

            case stripos($field, 'Solution Hindi') === 0:
                $current['solution']['text']['hi'] .= " " . $cleanValue;
                $current['solution']['images'] = array_merge($current['solution']['images'], $images);
                break;

            case stripos($field, 'Specialization') === 0:
                $current['specialization'] = array_filter(array_map('trim', explode(',', $cleanValue)));
                break;
        }
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

        return $q;
    }

    /**
     * Extracts all [[IMAGE:...]] placeholders from a string, returning an array of the extracted paths.
     * The paths are cleaned of whitespace (robust against newlines/spaces).
     * @param string $text text to extract images from
     * @return array|null list of extracted image paths
     */
    private function imageExtraction(string $text) : array
    {
        $images = [];

        // Outer regex: find all [[...]] blocks (non-greedy, multiple times)
        if (preg_match_all('/\[\[([\s\S]*?)\]\]/i', $text, $blocks)) {
            foreach ($blocks[1] as $block) {
                // Inner regex: clean IMAGE path (robust against newlines/spaces)
                if (preg_match('/I\s*M\s*A\s*G\s*E\s*:\s*([\s\S]+)/i', $block, $m)) {
                    $cleanPath = preg_replace('/\s+/', '', $m[1]);
                    $images[] = $cleanPath;
                }
            }
        }

            // Remove all [[IMAGE...]] from text
            // $text = preg_replace('/\[\[[\s\S]*?\]\]/', '', $value);
            // $text = trim(preg_replace('/\s+/', ' ', $text));
        return $images;
    }
    
}


// (new \App\Helpers\QuestionParserFromText("/var/www/myproject/easyway_yt/public/word_files/2025_08_21_06_25:9782/output.txt"))->parse(); 
// (new \App\Helpers\QuestionParserFromText("/var/www/myproject/easyway_yt/public/word_files/2025_08_21_06_54:6307/output.txt"))->parse(); 
