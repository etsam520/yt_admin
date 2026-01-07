<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\class\Question;
use App\Http\Controllers\Controller;
use App\Models\QuestionTbl;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;

class PdfGeneratorController extends Controller
{
    protected $pdfService;

    public function __construct(PdfGeneratorService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate PDF with questions
     */
    public function generateQuestionsPdf(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                // 'questions' => 'nullable|array',
                // 'questions.*' => 'integer|exists:question_tbls,id',
                'template' => 'nullable|string|in:standard,modern,minimal,exam,bilingual,magazine',
                'language' => 'nullable|string|in:hindi,english,both',
                'options' => 'nullable|array',
                'options.show_solutions' => 'nullable|boolean',
                'options.show_question_numbers' => 'nullable|boolean',
                'options.show_answer_key' => 'nullable|boolean',
                'options.highlight_correct_answers' => 'nullable|boolean',
                'options.show_images' => 'nullable|boolean',
                'options.show_difficulty_level' => 'nullable|boolean',
                'options.show_category_info' => 'nullable|boolean',
                'options.show_ca_date' => 'nullable|boolean',
                'options.group_by_subject' => 'nullable|boolean',
                'options.group_by_topic' => 'nullable|boolean',
                'options.page_size' => 'nullable|string|in:A4,A3,Letter,Legal',
                'options.orientation' => 'nullable|string|in:portrait,landscape',
                'options.font_size' => 'nullable|integer|min:8|max:20',
                'options.background_color' => 'nullable|string',
                'options.text_color' => 'nullable|string',
                'options.header_text' => 'nullable|string',
                'options.footer_text' => 'nullable|string',
                'options.watermark' => 'nullable|string',
                'options.include_qr_code' => 'nullable|boolean',
                'options.two_column_layout' => 'nullable|boolean',
                'options.answers_on_separate_page' => 'nullable|boolean',
                'options.column_count' => 'nullable|integer|min:1|max:4',
                'options.column_gap' => 'nullable|string',
                'options.column_rule' => 'nullable|string',
                'options.page_padding' => 'nullable|string',
                'options.render_math' => 'nullable|boolean',
                'options.math_engine' => 'nullable|string|in:katex,mathjax',
            ]);

            // Fetch questions with the same structure as previewPdf
            $questions = QuestionTbl::with(['meta'])
            ->leftJoin('question_categories as topic', function ($join) {
                $join->on('topic.id', '=', 'question_tbls.category_id')
                    ->where('topic.depth_index', '=', 'topic');
            })
            ->leftJoin('question_categories as chapter', function ($join) {
                $join->on('chapter.id', '=', 'topic.parent_id')
                    ->where('chapter.depth_index', '=', 'chapter');
            })
            ->leftJoin('question_categories as subject', function ($join) {
                $join->on('subject.id', '=', 'chapter.parent_id')
                    ->where('subject.depth_index', '=', 'subject');
            })
            ->leftJoin('question_metas as group_meta', function ($join)  {
                $join->on('group_meta.question_id', '=', 'question_tbls.id')
                    ->where('group_meta.meta_key', '=', 'group');
            })
            // ->whereIn('question_tbls.id', $validated['questions'])
            ->selectRaw('
                question_tbls.*,
                JSON_EXTRACT(question_tbls.question, "$.text.en") as question_text_en,
                JSON_EXTRACT(question_tbls.question, "$.text.hi") as question_text_hi,
                JSON_EXTRACT(question_tbls.options, "$[*].text.en") as options_text_en,
                JSON_EXTRACT(question_tbls.options, "$[*].text.hi") as options_text_hi,
                subject.name as subject_name,
                chapter.name as chapter_name,
                topic.name as topic_name,
                IFNULL(group_meta.meta_value, "pyq") as group_name,
                (
                    SELECT qm.meta_value 
                    FROM question_metas qm
                    WHERE qm.question_id = question_tbls.id 
                    AND qm.meta_key = "ca_date" 
                    LIMIT 1
                ) as ca_date
            ')
            ->get();
         
            $questions = $questions->map(function ($question) : object {
                $_newQuestion = new stdClass();
                $_newQuestion->id = $question->id;
                $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
                $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
                $_newQuestion->subject_name = $question->subject_name;
                $_newQuestion->chapter_name = $question->chapter_name;
                $_newQuestion->topic_name = $question->topic_name;
                $_newQuestion->group_name = $question->group_name;
                $_newQuestion->ca_date = $question->ca_date;
                $_newQuestion->formattedQuestion = (new Question($question))->toObject();

                return $_newQuestion;
            });

            if ($questions->isEmpty()) {
                return response()->json(['error' => 'No questions found'], 404);
            }

            // Set comprehensive default options with enhanced layout settings
            $defaultOptions = [
                // Display options
                'show_solutions' => true,
                'show_question_numbers' => true,
                'show_answer_key' => false,
                'highlight_correct_answers' => false,
                'show_images' => true,
                'show_difficulty_level' => false,
                'show_category_info' => true,
                'show_ca_date' => true,
                
                // Grouping options
                'group_by_subject' => false,
                'group_by_topic' => false,
                
                // Page layout
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'font_size' => 11,
                'line_height' => 1.6,
                
                // Colors and styling
                'background_color' => '#ffffff',
                'text_color' => '#333333',
                'header_color' => '#cc0000',
                'accent_color' => '#0066cc',
                
                // Advanced features
                'include_qr_code' => false,
                'two_column_layout' => true,
                'answers_on_separate_page' => false,
                
                // Column settings
                'column_count' => 2,
                'column_gap' => 10,
                'column_rule' => '1px solid #ddd',
                
                // Margins and padding
                'page_padding' => 15,
                'top_margin' => 20,
                'bottom_margin' => 15,
                'left_margin' => 15,
                'right_margin' => 15,
                
                // Math rendering
                'render_math' => true,
                'math_engine' => 'katex',
                
                // PDF metadata
                'title' => 'Question Bank',
                'author' => 'YT Education',
                'subject' => 'Exam Questions',
            ];

            // Merge and validate options
            $options = array_merge($defaultOptions, $validated['options'] ?? []);
            $language = $validated['language'] ?? 'both';
            $template = $validated['template'] ?? 'magazine';

            // Validate and sanitize options
            $options = $this->sanitizeOptions($options);
            
            // Add metadata to options
            $options['metadata'] = [
                'total_questions' => $questions->count(),
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'language' => $language,
                'template' => $template,
            ];
            
            // Log PDF generation request
            Log::info('PDF Generation Started', [
                'question_count' => $questions->count(),
                'template' => $template,
                'language' => $language,
            ]);

            // Generate PDF with enhanced error handling
            $pdfPath = $this->pdfService->generateQuestionsPdf(
                $questions,
                $template,
                $options,
                $language
            );
            // dd($pdfPath);
            // Verify PDF was created
            if (!Storage::disk('public')->exists($pdfPath)) {
                throw new \Exception('PDF file was not created successfully');
            }
            
            $fileSize = Storage::disk('public')->size($pdfPath);
            Log::info('PDF Generation Completed', [
                'path' => $pdfPath,
                'size' => $fileSize,
            ]);

            return response()->json([
                'success' => true,
                'pdf_url' => asset('storage/' . $pdfPath),
                'pdf_path' => $pdfPath,
                'file_size' => $this->formatBytes($fileSize),
                'question_count' => $questions->count(),
                'template' => $template,
                'language' => $language,
                'message' => 'PDF generated successfully with ' . $questions->count() . ' questions'
            ]);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Preview PDF HTML content
     */
    public function previewPdf(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'questions' => 'array|nullable',
                'questions.*' => 'integer|exists:question_tbls,id',
                'template' => 'string|in:standard,modern,minimal,exam,bilingual',
                'language' => 'nullable|string|in:hindi,english,both',
                'options' => 'array|nullable',
            ]);

            // Fetch questions
            $questions = QuestionTbl::with(['meta'])
            ->leftJoin('question_categories as topic', function ($join) {
                $join->on('topic.id', '=', 'question_tbls.category_id')
                    ->where('topic.depth_index', '=', 'topic');
            })
            ->leftJoin('question_categories as chapter', function ($join) {
                $join->on('chapter.id', '=', 'topic.parent_id')
                    ->where('chapter.depth_index', '=', 'chapter');
            })
            ->leftJoin('question_categories as subject', function ($join) {
                $join->on('subject.id', '=', 'chapter.parent_id')
                    ->where('subject.depth_index', '=', 'subject');
            })
            ->leftJoin('question_metas as group_meta', function ($join)  {
                $join->on('group_meta.question_id', '=', 'question_tbls.id')
                    ->where('group_meta.meta_key', '=', 'group');
            })
            ->selectRaw('
                question_tbls.*,
                JSON_EXTRACT(question_tbls.question, "$.text.en") as question_text_en,
                JSON_EXTRACT(question_tbls.question, "$.text.hi") as question_text_hi,
                JSON_EXTRACT(question_tbls.options, "$[*].text.en") as options_text_en,
                JSON_EXTRACT(question_tbls.options, "$[*].text.hi") as options_text_hi,
                subject.name as subject_name,
                chapter.name as chapter_name,
                topic.name as topic_name,
                IFNULL(group_meta.meta_value, "pyq") as group_name,
                (
                    SELECT qm.meta_value 
                    FROM question_metas qm
                    WHERE qm.question_id = question_tbls.id 
                    AND qm.meta_key = "ca_date" 
                    LIMIT 1
                ) as ca_date
            ')
            // ->having('group_name', '=', $group) 
            ->get();
            $ques = $questions->map(function ($question) : object {
                $_newQuestion = new stdClass(); // Initialize an empty standard class object
                $_newQuestion->id = $question->id;
                $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
                $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
                $_newQuestion->subject_name = $question->subject_name;
                $_newQuestion->chapter_name = $question->chapter_name;
                $_newQuestion->topic_name = $question->topic_name;
                $_newQuestion->group_name = $question->group_name;
                $_newQuestion->ca_date = $question->ca_date;
                $_newQuestion->formattedQuestion = (new Question($question))->toObject();

                return $_newQuestion; // Corrected variable name
            });

            // Generate HTML preview
            $htmlContent = $this->pdfService->generateHtmlPreview(
                $ques,
                $request->get('template', 'bilingual'),
                $request->get('options', []),
                $request->get('language', 'both')
            );

            return response()->json([
                'success' => true,
                'html_content' => $htmlContent
            ]);

        } catch (\Exception $e) {
            Log::error('PDF Preview Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate preview: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Sanitize and validate PDF options
     */
    protected function sanitizeOptions(array $options): array
    {
        // Sanitize numeric values
        $options['font_size'] = max(8, min(20, intval($options['font_size'] ?? 11)));
        $options['column_count'] = max(1, min(4, intval($options['column_count'] ?? 2)));
        $options['column_gap'] = max(5, min(20, intval($options['column_gap'] ?? 10)));
        $options['page_padding'] = max(10, min(30, intval($options['page_padding'] ?? 15)));
        $options['top_margin'] = max(10, min(40, intval($options['top_margin'] ?? 20)));
        $options['bottom_margin'] = max(10, min(30, intval($options['bottom_margin'] ?? 15)));
        $options['left_margin'] = max(10, min(30, intval($options['left_margin'] ?? 15)));
        $options['right_margin'] = max(10, min(30, intval($options['right_margin'] ?? 15)));
        $options['line_height'] = max(1.0, min(2.5, floatval($options['line_height'] ?? 1.6)));
        
        // Sanitize color values
        $colorFields = ['background_color', 'text_color', 'header_color', 'accent_color'];
        foreach ($colorFields as $field) {
            if (isset($options[$field])) {
                $options[$field] = $this->sanitizeColor($options[$field]);
            }
        }
        
        // Ensure boolean values
        $booleanFields = [
            'show_solutions', 'show_question_numbers', 'show_answer_key',
            'highlight_correct_answers', 'show_images', 'show_difficulty_level',
            'show_category_info', 'show_ca_date', 'group_by_subject',
            'group_by_topic', 'include_qr_code', 'two_column_layout',
            'answers_on_separate_page', 'render_math'
        ];
        foreach ($booleanFields as $field) {
            if (isset($options[$field])) {
                $options[$field] = filter_var($options[$field], FILTER_VALIDATE_BOOLEAN);
            }
        }
        
        return $options;
    }
    
    /**
     * Sanitize color values
     */
    protected function sanitizeColor(string $color): string
    {
        // Remove any whitespace
        $color = trim($color);
        
        // Check if it's a valid hex color
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return $color;
        }
        
        // If not valid, return default
        return '#333333';
    }
    
    /**
     * Format bytes to human-readable size
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Get available PDF templates
     */
    public function getTemplates(): JsonResponse
    {
        $templates = [
            [
                'id' => 'magazine',
                'name' => 'Magazine Template',
                'description' => 'Professional multi-column magazine-style layout with intelligent content flow and MathJax support',
                'preview' => asset('images/templates/magazine-preview.png'),
                'features' => ['Multi-Column Layout', 'Math Formula Support', 'Professional Design', 'Continuous Flow']
            ],
            [
                'id' => 'bilingual',
                'name' => 'Bilingual Template',
                'description' => 'Advanced template with Hindi/English language support and modern design',
                'preview' => asset('images/templates/bilingual-preview.png'),
                'features' => ['Language Options', 'Answer Key', 'Two Column Layout', 'Category Grouping']
            ],
            [
                'id' => 'standard',
                'name' => 'Standard Template',
                'description' => 'Clean and simple layout with basic formatting',
                'preview' => asset('images/templates/standard-preview.png'),
                'features' => ['Simple Layout', 'Question Numbers', 'Solutions']
            ],
            [
                'id' => 'modern',
                'name' => 'Modern Template',
                'description' => 'Contemporary design with colored sections',
                'preview' => asset('images/templates/modern-preview.png'),
                'features' => ['Modern Design', 'Colored Sections', 'Visual Appeal']
            ],
            [
                'id' => 'minimal',
                'name' => 'Minimal Template',
                'description' => 'Minimalist design with maximum content focus',
                'preview' => asset('images/templates/minimal-preview.png'),
                'features' => ['Minimal Design', 'Content Focus', 'Clean']
            ],
            [
                'id' => 'exam',
                'name' => 'Exam Template',
                'description' => 'Formal exam paper layout with answer bubbles',
                'preview' => asset('images/templates/exam-preview.png'),
                'features' => ['Exam Format', 'Answer Bubbles', 'Formal Layout']
            ]
        ];

        $languageOptions = [
            ['id' => 'hindi', 'name' => 'हिंदी (Hindi Only)', 'description' => 'Display questions only in Hindi'],
            ['id' => 'english', 'name' => 'English Only', 'description' => 'Display questions only in English'],
            ['id' => 'both', 'name' => 'द्विभाषी / Bilingual', 'description' => 'Display questions in both Hindi and English']
        ];

        return response()->json([
            'success' => true,
            'templates' => $templates,
            'language_options' => $languageOptions,
            'advanced_options' => [
                'show_solutions' => 'Show detailed solutions for each question',
                'show_answer_key' => 'Display answer key on separate page',
                'highlight_correct_answers' => 'Highlight correct answers in green',
                'show_category_info' => 'Show subject, chapter, and topic information',
                'group_by_subject' => 'Group questions by subject with headers',
                'group_by_topic' => 'Group questions by topic with headers',
                'two_column_layout' => 'Use two-column layout for space efficiency',
                'answers_on_separate_page' => 'Put all answers on a separate page at the end',
                'include_qr_code' => 'Add QR code for digital tracking',
                'watermark' => 'Add custom watermark text',
                'column_count' => 'Number of columns (1-4) for magazine template',
                'column_gap' => 'Gap between columns (e.g., 8mm, 10mm)',
                'render_math' => 'Enable mathematical formula rendering',
                'math_engine' => 'Math rendering engine (katex or mathjax)',
            ]
        ]);
    }
}
