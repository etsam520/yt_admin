<?php

namespace App\Services;

use App\Helpers\class\pdfLayout\tcpdf\UserMagzinePdf;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PdfGeneratorService
{
    protected $converter;
    protected $tempPath;

    public function __construct()
    {
        $this->detectConverter();
        $this->tempPath = storage_path('app/temp/pdf/');
        
        // Ensure temp directory exists
        if (!file_exists($this->tempPath)) {
            mkdir($this->tempPath, 0755, true);
        }
        
        // Ensure storage directories exist
        $storagePaths = [
            storage_path('app/public'),
            storage_path('app/pdfs'),
            public_path('storage')
        ];
        
        foreach ($storagePaths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Detect available PDF converter
     */
    protected function detectConverter(): void
    {
        // Check for wkhtmltopdf
        $process = new Process(['which', 'wkhtmltopdf']);
        $process->run();
        
        if ($process->isSuccessful()) {
            $this->converter = 'wkhtmltopdf';
            return;
        }

        // Check for LibreOffice
        $process = new Process(['which', 'libreoffice']);
        $process->run();
        
        if ($process->isSuccessful()) {
            $this->converter = 'libreoffice';
            return;
        }

        // Check for lowriter
        $process = new Process(['which', 'lowriter']);
        $process->run();
        
        if ($process->isSuccessful()) {
            $this->converter = 'libreoffice';
            return;
        }

        throw new \Exception('No PDF converter found. Please install wkhtmltopdf or LibreOffice.');
    }

    /**
     * Generate PDF from questions
     */
    public function generateQuestionsPdf(Collection $questions, string $template, array $options, string $language = 'both'): string
    {
        // Group questions if needed
        if (!empty($options['group_by_subject'])) {
            $questions = $this->groupQuestionsBySubject($questions);
        } elseif (!empty($options['group_by_topic'])) {
            $questions = $this->groupQuestionsByTopic($questions);
        }

        // Generate PDF
        $pdfFile = $this->tempPath . uniqid('questions_') . '.pdf';
        
        // Prepare magazine data for PDF generation
        $magazineData = $this->prepareMagazineData($questions, $options, $language, $template);
        
        // dd($magazineData);
        // Generate PDF with TCPDF
        $magazinePdf = new UserMagzinePdf(fileName: $pdfFile);
        $magazinePdf->generatePdf($magazineData);
        
        // Verify PDF was created
        if (!file_exists($pdfFile) || filesize($pdfFile) === 0) {
            throw new \Exception("PDF file was not generated or is empty");
        }
        
        // Move to storage
        $storagePath = 'pdfs/' . date('Y/m/d/') . basename($pdfFile);
        Storage::disk('public')->put($storagePath, file_get_contents($pdfFile));

        // Cleanup temp file
        unlink($pdfFile);

        return $storagePath;
    }
    
    /**
     * Prepare magazine data structure for PDF generation
     */
    protected function prepareMagazineData(Collection $questions, array $options, string $language, string $template): array
    {
        return [
            'title' => $options['title'] ?? 'Question Bank',
            'author' => $options['author'] ?? 'YT Education',
            'subject' => $options['subject'] ?? 'Exam Questions',
            'questions' => $questions,
            'options' => $options,
            'language' => $language,
            'template' => $template,
            'metadata' => $options['metadata'] ?? [],
            'columns' => $options['column_count'] ?? 2,
            'column_gap' => $options['column_gap'] ?? 10,
            'font_size' => $options['font_size'] ?? 11,
            'line_height' => $options['line_height'] ?? 1.6,
            'colors' => [
                'header' => $options['header_color'] ?? '#cc0000',
                'text' => $options['text_color'] ?? '#333333',
                'accent' => $options['accent_color'] ?? '#0066cc',
                'background' => $options['background_color'] ?? '#ffffff',
            ],
            'show_solutions' => $options['show_solutions'] ?? true,
            'show_question_numbers' => $options['show_question_numbers'] ?? true,
            'show_category_info' => $options['show_category_info'] ?? true,
            'show_ca_date' => $options['show_ca_date'] ?? true,
            'render_math' => $options['render_math'] ?? true,
        ];
    }

    /**
     * Generate HTML preview
     */
    public function generateHtmlPreview(Collection $questions, string $template, array $options, string $language = 'both'): string
    {
        return $this->generateHtmlContent($questions, $template, $options, $language);
    }

    /**
     * Generate HTML content for PDF
     */
    protected function generateHtmlContent(Collection $questions, string $template, array $options, string $language = 'both'): string
    {
        // Process questions to convert image URLs to local paths
        $processedQuestions = $this->processImagesForPdf($questions);
        
        $data = [
            'questions' => $processedQuestions,
            'options' => $options,
            'template' => $template,
            'language' => $language,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        $htmlContent = View::make("pdf.templates.{$template}", $data)->render();
        
        // Convert any remaining asset() URLs to file:// URLs
        $htmlContent = $this->convertAssetUrlsToLocalPaths($htmlContent);
        
        return $htmlContent;
    }

    /**
     * Process questions to convert image paths for PDF generation
     */
    protected function processImagesForPdf(Collection $questions): Collection
    {
        return $questions->map(function ($question) {
            if (isset($question->formattedQuestion)) {
                // Process question images
                if (isset($question->formattedQuestion->question->images)) {
                    $question->formattedQuestion->question->images = $this->convertImagePaths($question->formattedQuestion->question->images);
                }
                
                // Process option images
                if (isset($question->formattedQuestion->options)) {
                    foreach ($question->formattedQuestion->options as $option) {
                        if (isset($option->images)) {
                            $option->images = $this->convertImagePaths($option->images);
                        }
                    }
                }
                
                // Process solution images
                if (isset($question->formattedQuestion->solution->images)) {
                    $question->formattedQuestion->solution->images = $this->convertImagePaths($question->formattedQuestion->solution->images);
                }
            }
            
            return $question;
        });
    }

    /**
     * Convert image paths from storage references to absolute file paths
     */
    protected function convertImagePaths(array $images): array
    {
        return array_map(function ($imagePath) {
            // If it's already a full path, return as is
            if (file_exists($imagePath)) {
                return 'file://' . $imagePath;
            }
            
            // Try to resolve from storage
            $storagePath = storage_path('app/public/' . ltrim($imagePath, '/'));
            if (file_exists($storagePath)) {
                return 'file://' . $storagePath;
            }
            
            // Try to resolve from public directory
            $publicPath = public_path('storage/' . ltrim($imagePath, '/'));
            if (file_exists($publicPath)) {
                return 'file://' . $publicPath;
            }
            
            // Return original path if nothing else works
            return $imagePath;
        }, $images);
    }

    /**
     * Convert asset URLs to local file paths for wkhtmltopdf
     */
    protected function convertAssetUrlsToLocalPaths(string $htmlContent): string
    {
        return preg_replace_callback('/src="([^"]+)"/', function ($matches) {
            $url = $matches[1];
            
            // Skip if already a file:// URL
            if (strpos($url, 'file://') === 0) {
                return $matches[0];
            }
            
            // Handle direct image references (just filename or ID)
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                // Try to find the image in common upload directories
                $possiblePaths = [
                    public_path('uploads/all/' . $url),
                    public_path('storage/uploads/all/' . $url),
                    storage_path('app/public/uploads/all/' . $url),
                    public_path('uploads/' . $url),
                    public_path('storage/' . $url),
                    storage_path('app/public/' . $url),
                ];
                
                // Also try common extensions if no extension is provided
                if (!pathinfo($url, PATHINFO_EXTENSION)) {
                    $extensions = ['.jpg', '.jpeg', '.png', '.gif', '.svg'];
                    foreach ($extensions as $ext) {
                        $possiblePaths[] = public_path('uploads/all/' . $url . $ext);
                        $possiblePaths[] = public_path('storage/uploads/all/' . $url . $ext);
                        $possiblePaths[] = storage_path('app/public/uploads/all/' . $url . $ext);
                    }
                }
                
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        return 'src="file://' . $path . '"';
                    }
                }
                
                // If no file found, mark for removal
                Log::warning("Image not found for PDF generation: {$url}");
                return 'data-remove-image="true" src=""';
            }
            
            // Handle HTTP URLs pointing to local assets
            if (strpos($url, 'http://localhost') === 0 || strpos($url, 'https://localhost') === 0) {
                // Extract path from URL
                $parsedUrl = parse_url($url);
                $path = $parsedUrl['path'] ?? '';
                
                // Try different possible local paths
                $possiblePaths = [
                    public_path($path),
                    public_path('storage' . $path),
                    storage_path('app/public' . $path),
                ];
                
                foreach ($possiblePaths as $localPath) {
                    if (file_exists($localPath)) {
                        return 'src="file://' . $localPath . '"';
                    }
                }
            }
            
            // If we can't find the file, mark for removal
            Log::warning("Asset not found for PDF generation: {$url}");
            return 'data-remove-image="true" src=""';
        }, $htmlContent);
        
        // Remove img tags marked for removal
        $htmlContent = preg_replace('/<img[^>]*data-remove-image="true"[^>]*>/i', '', $htmlContent);
        
        return $htmlContent;
    }

    /**
     * Generate PDF using wkhtmltopdf
     */
    protected function generateWithWkhtmltopdf(string $htmlFile, string $pdfFile, array $options): void
    {
        $command = [
            'wkhtmltopdf',
            '--page-size', $options['page_size'] ?? 'A4',
            '--orientation', $options['orientation'] ?? 'Portrait',
            '--margin-top', '15mm',
            '--margin-bottom', '15mm',
            '--margin-left', '15mm',
            '--margin-right', '15mm',
            '--encoding', 'UTF-8',
            '--enable-local-file-access',
            '--load-error-handling', 'ignore',
            '--load-media-error-handling', 'ignore',
            '--disable-external-links',
            '--no-stop-slow-scripts',
        ];

        // Add header if specified
        if (!empty($options['header_text'])) {
            $command[] = '--header-center';
            $command[] = $options['header_text'];
            $command[] = '--header-font-size';
            $command[] = '10';
        }

        // Add footer if specified
        if (!empty($options['footer_text'])) {
            $command[] = '--footer-center';
            $command[] = $options['footer_text'];
            $command[] = '--footer-font-size';
            $command[] = '8';
        }

        $command[] = $htmlFile;
        $command[] = $pdfFile;

        $process = new Process($command);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            // Log detailed error information
            Log::error('wkhtmltopdf failed', [
                'command' => implode(' ', $command),
                'exit_code' => $process->getExitCode(),
                'output' => $process->getOutput(),
                'error_output' => $process->getErrorOutput(),
                'html_file' => $htmlFile,
                'pdf_file' => $pdfFile,
            ]);
            
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Generate PDF using LibreOffice
     */
    protected function generateWithLibreOffice(string $htmlFile, string $pdfFile, array $options): void
    {
        $command = [
            'libreoffice',
            '--headless',
            '--convert-to',
            'pdf',
            '--outdir',
            dirname($pdfFile),
            $htmlFile
        ];

        $process = new Process($command);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // LibreOffice creates file with different name
        $generatedPdf = dirname($pdfFile) . '/' . pathinfo($htmlFile, PATHINFO_FILENAME) . '.pdf';
        if (file_exists($generatedPdf)) {
            rename($generatedPdf, $pdfFile);
        }
    }

    /**
     * Group questions by subject
     */
    protected function groupQuestionsBySubject(Collection $questions): Collection
    {
        $grouped = $questions->groupBy('subject_name');
        $result = collect();
        
        foreach ($grouped as $subject => $subjectQuestions) {
            $result->push((object)[
                'is_subject_header' => true,
                'subject_name' => $subject,
                'question_count' => $subjectQuestions->count()
            ]);
            
            foreach ($subjectQuestions as $question) {
                $result->push($question);
            }
        }
        
        return $result;
    }

    /**
     * Group questions by topic
     */
    protected function groupQuestionsByTopic(Collection $questions): Collection
    {
        $grouped = $questions->groupBy(function ($question) {
            return ($question->subject_name ?? 'Other') . '|' . ($question->topic_name ?? 'General');
        });
        
        $result = collect();
        
        foreach ($grouped as $key => $topicQuestions) {
            [$subject, $topic] = explode('|', $key);
            
            $result->push((object)[
                'is_topic_header' => true,
                'subject_name' => $subject,
                'topic_name' => $topic,
                'question_count' => $topicQuestions->count()
            ]);
            
            foreach ($topicQuestions as $question) {
                $result->push($question);
            }
        }
        
        return $result;
    }
}
