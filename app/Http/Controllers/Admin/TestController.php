<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TestQuestionsImport;
use App\Models\Test;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
class TestController extends Controller
{

    public function switchLanguage(Request $request, Test $test)
    {
        $request->validate([
            'language' => 'required|in:en,hi',
        ]);

        session(['language' => $request->language]);
        return redirect()->back();
    }

    public function index()
    {
        $tests = Test::paginate(10);
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_name' => 'required|string|max:255',
            'series_name' => 'nullable|string|max:255',
            'type' => 'required|in:free,paid',
            'test_instructions' => 'nullable|string',
            'no_of_questions' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'total_duration_minutes' => 'required|integer|min:1',
            'maximum_attempts' => 'required|integer|min:1',
            'is_shuffle_questions' => 'boolean',
            'is_display_pause_option' => 'boolean',
            'is_display_rank' => 'boolean',
            'is_show_total_students' => 'boolean',
            'solution_pdf' => 'nullable|file|mimes:pdf|max:5048',
            'solution_video' => 'nullable|file|mimes:mp4|max:30240',
            'is_show_solution_pdf' => 'boolean',
            'is_show_solution_video' => 'boolean',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'test_material_pdf' => 'nullable|file|mimes:pdf|max:5048',
            'is_allow_download_material' => 'boolean',
            'is_published' => 'boolean',
        ]);

        try {
            $data = $request->all();

            if ($request->hasFile('solution_pdf')) {
                $data['solution_pdf'] = $request->file('solution_pdf')->store('solutions', 'public');
            }
            if ($request->hasFile('solution_video')) {
                $data['solution_video'] = $request->file('solution_video')->store('videos', 'public');
            }
            if ($request->hasFile('test_material_pdf')) {
                $data['test_material_pdf'] = $request->file('test_material_pdf')->store('materials', 'public');
            }

            Test::create($data);
            return redirect()->route('admin.tests.index')->with('success', 'Test created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkUploadQuestions(Test $test)
    {
        return view('admin.tests.bulk-upload-questions', compact('test'));
    }

    public function bulkImportQuestions(Request $request, Test $test)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,doc,docx|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Handle Excel files
                Excel::import(new TestQuestionsImport($test->id), $file);
            } elseif (in_array($extension, ['doc', 'docx'])) {
                // Handle Word files
                $questions = $this->parseWordFile($file);
                if (empty($questions)) {
                    return redirect()->back()->with('error', 'No valid questions found in the Word document. Please use the correct format.');
                }
                foreach ($questions as $questionData) {
                    \App\Models\TestQuestion::create([
                        'test_id' => $test->id,
                        'question_text' => $questionData['question_text'],
                        'question_text_hindi' => $questionData['question_text_hindi'],
                        'option_a' => $questionData['option_a'],
                        'option_a_hindi' => $questionData['option_a_hindi'],
                        'option_b' => $questionData['option_b'],
                        'option_b_hindi' => $questionData['option_b_hindi'],
                        'option_c' => $questionData['option_c'],
                        'option_c_hindi' => $questionData['option_c_hindi'],
                        'option_d' => $questionData['option_d'],
                        'option_d_hindi' => $questionData['option_d_hindi'],
                        'correct_option' => $questionData['correct_option'],
                        'solution' => $questionData['solution'],
                        'solution_hindi' => $questionData['solution_hindi'],
                    ]);
                }
            } else {
                return redirect()->back()->with('error', 'Unsupported file format.');
            }

            $test->update(['no_of_questions' => $test->questions->count()]);
            return redirect()->route('admin.tests.index')->with('success', 'Questions imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors($errorMessages)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function parseWordFile($file)
    {
        $phpWord = IOFactory::load($file->getRealPath());
        $questions = [];
        $currentQuestion = null;

        foreach ($phpWord->getSections() as $section) {
            $texts = [];
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text = trim($element->getText());
                    if (!empty($text)) {
                        $texts[] = $text;
                    }
                }
            }

            // Process the collected text lines
            for ($i = 0; $i < count($texts); $i++) {
                $line = $texts[$i];

                if (preg_match('/^Question\s*\d+:\s*(.+)\s\(English\)$/', $line, $matches)) {
                    if ($currentQuestion) {
                        $questions[] = $currentQuestion;
                    }
                    $currentQuestion = [
                        'question_text' => $matches[1],
                        'question_text_hindi' => '',
                        'option_a' => '',
                        'option_a_hindi' => '',
                        'option_b' => '',
                        'option_b_hindi' => '',
                        'option_c' => '',
                        'option_c_hindi' => '',
                        'option_d' => '',
                        'option_d_hindi' => '',
                        'correct_option' => '',
                        'solution' => '',
                        'solution_hindi' => '',
                    ];
                } elseif ($currentQuestion && preg_match('/^Question\s*\d+:\s*(.+)\s\(Hindi\)$/', $line, $matches)) {
                    $currentQuestion['question_text_hindi'] = $matches[1];
                } elseif ($currentQuestion && preg_match('/^Option\s*[A-D]:\s*(.+)\s\(English\)$/', $line, $matches)) {
                    $option = strtolower(substr($matches[0], 7, 1)); // Extract A, B, C, or D
                    $currentQuestion["option_{$option}"] = $matches[1];
                } elseif ($currentQuestion && preg_match('/^Option\s*[A-D]:\s*(.+)\s\(Hindi\)$/', $line, $matches)) {
                    $option = strtolower(substr($matches[0], 7, 1)); // Extract A, B, C, or D
                    $currentQuestion["option_{$option}_hindi"] = $matches[1];
                } elseif ($currentQuestion && preg_match('/^Correct\s*Option:\s*([a-d])$/', $line, $matches)) {
                    $currentQuestion['correct_option'] = $matches[1];
                } elseif ($currentQuestion && preg_match('/^Solution:\s*(.+)\s\(English\)$/', $line, $matches)) {
                    $currentQuestion['solution'] = $matches[1];
                } elseif ($currentQuestion && preg_match('/^Solution:\s*(.+)\s\(Hindi\)$/', $line, $matches)) {
                    $currentQuestion['solution_hindi'] = $matches[1];
                }
            }
        }

        if ($currentQuestion) {
            $questions[] = $currentQuestion;
        }

        return $questions;
    }

    public function togglePublish(Test $test)
    {
        $test->update(['is_published' => !$test->is_published]);
        return redirect()->back()->with('success', 'Test ' . ($test->is_published ? 'published' : 'unpublished') . ' successfully.');
    }

    public function showQuestions(Test $test)
    {
        $questions = $test->questions()->paginate(10);
        return view('admin.tests.show-questions', compact('test', 'questions'));
    }

    public function edit(Test $test)
    {
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $request->validate([
            'test_name' => 'required|string|max:255',
            'series_name' => 'nullable|string|max:255',
            'type' => 'required|in:free,paid',
            'test_instructions' => 'nullable|string',
            'no_of_questions' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'total_duration_minutes' => 'required|integer|min:1',
            'maximum_attempts' => 'required|integer|min:1',
            'is_shuffle_questions' => 'boolean',
            'is_display_pause_option' => 'boolean',
            'is_display_rank' => 'boolean',
            'is_show_total_students' => 'boolean',
            'solution_pdf' => 'nullable|file|mimes:pdf|max:5048',
            'solution_video' => 'nullable|file|mimes:mp4|max:30240',
            'is_show_solution_pdf' => 'boolean',
            'is_show_solution_video' => 'boolean',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'test_material_pdf' => 'nullable|file|mimes:pdf|max:5048',
            'is_allow_download_material' => 'boolean',
            'is_published' => 'boolean',
        ]);

        try {
            $data = $request->all();

            if ($request->hasFile('solution_pdf')) {
                $data['solution_pdf'] = $request->file('solution_pdf')->store('solutions', 'public');
            }
            if ($request->hasFile('solution_video')) {
                $data['solution_video'] = $request->file('solution_video')->store('videos', 'public');
            }
            if ($request->hasFile('test_material_pdf')) {
                $data['test_material_pdf'] = $request->file('test_material_pdf')->store('materials', 'public');
            }

            $test->update($data);
            return redirect()->route('admin.tests.index')->with('success', 'Test updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function viewDetails(Test $test)
    {
        return view('admin.tests.view-details', compact('test'));
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Test deleted successfully.');
    }
}
