<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\class\Question;
use App\Http\Controllers\Controller;
use App\Models\QuestionTbl;
use App\Models\SetPdf;
use App\Services\TcpdfGeneratorService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use stdClass;

/**
 * TCPDF-based PDF Generator Controller
 * Uses PHP-based TCPDF library instead of command-line tools
 */
class TcpdfPdfGeneratorController extends Controller
{
    protected $pdfService;

    public function __construct(TcpdfGeneratorService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generate PDF with questions using TCPDF
     */
    public function generatePdf(Request $request)
    {
        try {
            $validated = $request->validate([
                'set_id' => 'required|integer',
                'template' => 'nullable|string|in:standard,modern,exam',
                'language' => 'nullable|string|in:hindi,english,both',
                'options' => 'nullable|array',
                'options.show_solutions' => 'nullable|boolean',
                'options.show_answer_key' => 'nullable|boolean',
                'options.show_images' => 'nullable|boolean',
                'options.group_by_subject' => 'nullable|boolean',
                'options.group_by_topic' => 'nullable|boolean',
                'options.orientation' => 'nullable|string|in:portrait,landscape',
                'options.header_text' => 'nullable|string',
                'options.footer_text' => 'nullable|string',
                'options.watermark' => 'nullable|string',
                'options.render_math' => 'nullable|boolean',
                'options.math_engine' => 'nullable|string|in:katex,mathjax',
                'show_cover_page' => 'nullable|boolean',
                'cover_page' => 'nullable|array',
                'cover_page.set_name' => 'nullable|string',
                'cover_page.teacher_name' => 'nullable|string',
                'cover_page.date' => 'nullable|string',
                // 'cover_page.image_path' => 'nullable|string',
                'cover_page.title_color' => 'nullable|string',
                'cover_page.subtitle_color' => 'nullable|string',

                'question.font_size' => 'nullable|integer|min:8|max:20',
                'question.color' => 'nullable|string',
                'question.background_color' => 'nullable|string',

                'question_options.font_size' => 'nullable|integer|min:8|max:20',
                'question_options.color' => 'nullable|string',
                'question_options.background_color' => 'nullable|string',

                'question_solution.font_size' => 'nullable|integer|min:8|max:20',
                'question_solution.color' => 'nullable|string',
                'question_solution.background_color' => 'nullable|string',
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
                ->leftJoin('question_metas as group_meta', function ($join) {
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
                ->get();

            $questions = $questions->map(function ($question): object {
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
            // Set default options
            $defaultOptions = [
                'show_solutions' => true,
                'show_answer_key' => false,
                'show_images' => true,
                'group_by_subject' => false,
                'group_by_topic' => false,
                'orientation' => 'portrait',
                'header_text' => '',
                'footer_text' => '',
                'watermark' => '',
                'render_math' => true,
                'math_engine' => 'katex',
                'show_cover_page' => true,
                'cover_page' => [
                    'set_name' => 'Question Set',
                    'teacher_name' => 'Teacher Name',
                    'date' => date('Y-m-d'),
                    'image_path' => asset('assets/user/img/logo/logo.png'),
                    'title_color' => '#2c5aa0',
                    'subtitle_color' => '#6b7280'
                ],

                'default_text_color' => '#1a1a1a',
                'default_background_color' => '#ffffff',
                'default_font_size' => 11,

                'question.font_size' => 11,
                'question.color' => '#1a1a1a',
                'question.background_color' => '#ffffff',

                'question_options.font_size' => 10,
                'question_options.color' => '#1a1a1a',
                'question_options.background_color' => '#f9f9f9',

                'question_solution.font_size' => 10,
                'question_solution.color' => '#1a1a1a',
                'question_solution.background_color' => '#f0f0f0',
            ];


            $options = array_merge($defaultOptions, $validated ?? []);
            $language = $validated['language'] ?? 'both';
            $template = $validated['template'] ?? 'magazine';

            // Generate PDF
            $res = $this->pdfService->generateQuestionsPdf(
                $questions,
                $template,
                $options,
                $language,
                false
            );
            if(gettype($res) == "string") :
           $pdf= SetPdf::create([
                'file_path' => $res,
                'url_path' => asset('storage/' . $res),
                'set_id' => $validated['set_id'],
            ]);

            return response()->json([
                'success' => true,
                'pdf_url' => route('set-pdf.show', ['id' => $pdf->id]),
                'message' => 'PDF generated successfully',
            ]);
            else :
                throw new Exception("PDF generation failed");
            endif;

        } catch (\Exception $e) {
            Log::error('TCPDF Generation Error: ' . $e->getMessage(). "file: ". $e->getFile(). " line: ". $e->getLine());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    } 

    public function setPdfsList(Request $request): JsonResponse
    {
        try {
            $setId = $request->query('setId');
            if (!$setId) {
                return response()->json(['error' => 'setId query parameter is required'], 400);
            }

            $setPdfs = SetPdf::select('id', 'created_at')->where('set_id', $setId)->get();
           
            return response()->json([
                'success' => true,
                'data' => $setPdfs,
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching Set PDFs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch Set PDFs: ' . $e->getMessage()], 500);
        }
    }
    public function deleteSetPdf(Request $request, $id): JsonResponse
    {
        try {
            $setPdf = SetPdf::find($id);
            if (!$setPdf) {
                return response()->json(['error' => 'Set PDF not found'], 404);
            }
            // Optionally, delete the physical file from storage
            Storage::delete($setPdf->file_path);
            $setPdf->delete();
            return response()->json([
                'success' => true,
                'message' => 'Set PDF deleted successfully',
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting Set PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete Set PDF: ' . $e->getMessage()], 500);
        }
    }

    public function showsetPdfById($setId) 
    {
        try {
            $setPdf = SetPdf::find($setId);
            if (!$setPdf) {
                return response()->json(['error' => 'Set PDF not found'], 404);
            }

            $content = file_get_contents("storage/" . $setPdf->file_path);
            if($content === false){
                throw new Exception("Failed to read PDF file");
            } 
            return response(
                content: $content,
                status: 200,
                headers: [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="questions.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            
        } catch (Exception $e) {
            Log::error('Error fetching Set PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch Set PDF: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Test PDF generation
     */
    public function testGeneratePdf(Request $request)
    {
        try {
            $validated = $request->validate([
                'template' => 'nullable|string|in:magazine,bilingual,standard,modern,minimal,exam',
                'language' => 'nullable|string|in:hindi,english,both',
                'options' => 'nullable|array',
                'options.show_solutions' => 'nullable|boolean',
                'options.show_question_numbers' => 'nullable|boolean',
                'options.show_answer_key' => 'nullable|boolean',
                'options.highlight_correct_answers' => 'nullable|boolean',
                'options.show_images' => 'nullable|boolean',
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
                'options.column_count' => 'nullable|integer|min:1|max:4',
                'options.column_gap' => 'nullable|string',
                'options.page_padding' => 'nullable|string',
                'options.render_math' => 'nullable|boolean',
                'options.math_engine' => 'nullable|string|in:katex,mathjax',
                'options.answers_on_separate_page' => 'nullable|boolean',
                'options.show_cover_page' => 'nullable|boolean',
                'options.cover_page' => 'nullable|array',
                'options.cover_page.set_name' => 'nullable|string',
                'options.cover_page.teacher_name' => 'nullable|string',
                'options.cover_page.date' => 'nullable|string',
                'options.cover_page.image_path' => 'nullable|string',
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
                ->leftJoin('question_metas as group_meta', function ($join) {
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
                ->get();

            $questions = $questions->map(function ($question): object {
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

            // Set default options
            $defaultOptions = [
                'show_solutions' => true,
                'show_question_numbers' => true,
                'show_answer_key' => false,
                'highlight_correct_answers' => false,
                'show_images' => true,
                'show_category_info' => true,
                'show_ca_date' => true,
                'group_by_subject' => false,
                'group_by_topic' => false,
                'page_size' => 'A4',
                'orientation' => 'portrait',
                'font_size' => 11,
                'background_color' => '#ffffff',
                'text_color' => '#1a1a1a',
                'column_count' => 2,
                'column_gap' => '8mm',
                'page_padding' => '15mm',
                'render_math' => true,
                'math_engine' => 'katex',
                'answers_on_separate_page' => false,
            ];

            $options = array_merge($defaultOptions, $validated['options'] ?? []);
            $language = $validated['language'] ?? 'both';
            $template = $validated['template'] ?? 'magazine';

            // Generate PDF
            return $this->pdfService->generateQuestionsPdf(
                $questions,
                $template,
                $options,
                $language
            );

            return response()->json([
                'success' => true,
                'pdf_url' => asset('storage/' . $pdfPath),
                'message' => 'PDF generated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('TCPDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
}
