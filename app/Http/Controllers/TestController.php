<?php

namespace App\Http\Controllers;

use App\Helpers\QuestionParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class TestController extends Controller
{
    public function convertDocxToText()
    {
        // Step 1: Load the .docx file (place your file in storage/app/word-files/)
        $docxPath = public_path('word_files/sample.docx');

        // dd(file_exists($docxPath));

        // Step 2: Read the Word file
        $phpWord = IOFactory::load($docxPath);

        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        // Step 3: Write to .txt file
        $txtFilePath = 'word_output/sample_output.txt';
        Storage::put(public_path($txtFilePath), $text, 'public');

        return response()->download(asset($txtFilePath, $text));

        // return response()->download(storage_path('app/' . $txtFilePath));
    }

        public function convertUsingDocx2txt()
    {
        // sudo apt install docx2txt
        $inputPath =    public_path('word_files/sample.docx');
        $outputPath = public_path('word_output/sample_output.txt');

        $command = "docx2txt \"$inputPath\" \"$outputPath\"";
        shell_exec($command);
        
        // return response()->download($outputPath);
    }

    public function convertDocxToHtml()
    {
        $inputPath = public_path('word_files/sample.docx');
        $outputDir = public_path('word_output/sample');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // LibreOffice conversion
        $command = "libreoffice --headless --convert-to html \"$inputPath\" --outdir \"$outputDir\"";
        shell_exec($command);

        // Find the output HTML file
        $outputFile = $outputDir . '/sample.html';

        if (!file_exists($outputFile)) {
            return response()->json(['error' => 'Conversion failed.']);
        }

        $htmlContent = file_get_contents($outputFile);

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    public function convertWithPandoc()
    {
        // $dirPrefix = 'word_files/'.date('Y_m_d_h_i')."-".rand(1000, 9999);
        $dirPrefix = 'word_files/2025_09_05_12_59-5961'; // /var/www/myproject/easyway_yt/public/word_files/2025_09_05_07_32-8963/output.txt
        // $inputPath  = public_path($dirPrefix.'/' . uniqid() . '_' . $file->getClientOriginalName());
        $outputPath = public_path($dirPrefix.'/output.html');
        $outputPath = public_path($dirPrefix.'/output.txt');
        $mediaPath  = public_path($dirPrefix.'/');

        $convertedText = file_exists($outputPath) ? file_get_contents($outputPath) : null;
        $logFIle = storage_path('logs/block_output.log');
        // file_put_contents($logFIle, "row text Block: \n" . $convertedText . "\n\n", FILE_APPEND);
        $parser = new QuestionParser();
        $questions = $parser->parse($convertedText);

        $cont = new \App\Http\Controllers\Api\Admin\QuestionController();
        $form = $cont->questionsPrettyFormat($questions,public_path($dirPrefix));
        // return true;
        dd($form);
    }

    public function convertDocxToHtmlWithMathAndImages()
    {
        $inputPath = public_path('word_files/Brochure.docx');
        $outputDir = public_path('word_output');
        $outputHtml = $outputDir . '/sample.html';
        $mediaDir = $outputDir . '/media';

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Clean previous output
        if (file_exists($outputHtml)) unlink($outputHtml);
        if (file_exists($mediaDir)) exec("rm -rf " . escapeshellarg($mediaDir));

        // Run pandoc conversion
        $command = "pandoc \"$inputPath\" -s -o \"$outputHtml\" --mathjax --extract-media=\"$mediaDir\"";
        shell_exec($command);

        if (!file_exists($outputHtml)) {
            return response()->json(['error' => 'Pandoc conversion failed.']);
        }

        $html = file_get_contents($outputHtml);

        // Modify media paths to be web-accessible (optional fix for browser)
        $html = str_replace('media/', asset('word_output/media/') . '/', $html);



        $mathjax = '';

        $html = $mathjax . $html;

        return response($html)->header('Content-Type', 'text/html');
    }

}
