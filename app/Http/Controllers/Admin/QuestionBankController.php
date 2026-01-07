<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeGroup;
use App\Models\Trade;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
class QuestionBankController extends Controller
{
    public function index()
    {
        $questions = Question::with('tradeGroup', 'trade', 'subject', 'chapter', 'topic')->paginate(10);
        return view('admin.question-bank.index', compact('questions'));
    }

    public function create()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.question-bank.create', compact('tradeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'topic_id' => 'required|exists:topics,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:a,b,c,d',
            'solution' => 'nullable|string',
        ]);

        // Verify hierarchy
        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();
        $subject = Subject::where('id', $request->subject_id)
                          ->where('trade_id', $request->trade_id)
                          ->firstOrFail();
        $chapter = Chapter::where('id', $request->chapter_id)
                          ->where('subject_id', $request->subject_id)
                          ->firstOrFail();
        $topic = Topic::where('id', $request->topic_id)
                      ->where('chapter_id', $request->chapter_id)
                      ->firstOrFail();

        Question::create([
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'topic_id' => $request->topic_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_option' => $request->correct_option,
            'solution' => $request->solution,
        ]);

        return redirect()->route('admin.question-bank.index')->with('success', 'Question added.');
    }

    public function edit(Question $question)
    {
        $tradeGroups = TradeGroup::all();
        $trades = Trade::where('trade_group_id', $question->trade_group_id)->get();
        $subjects = Subject::where('trade_id', $question->trade_id)->get();
        $chapters = Chapter::where('subject_id', $question->subject_id)->get();
        $topics = Topic::where('chapter_id', $question->chapter_id)->get();
        return view('admin.question-bank.edit', compact('question', 'tradeGroups', 'trades', 'subjects', 'chapters', 'topics'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'topic_id' => 'required|exists:topics,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:a,b,c,d',
            'solution' => 'nullable|string',
        ]);

        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();
        $subject = Subject::where('id', $request->subject_id)
                          ->where('trade_id', $request->trade_id)
                          ->firstOrFail();
        $chapter = Chapter::where('id', $request->chapter_id)
                          ->where('subject_id', $request->subject_id)
                          ->firstOrFail();
        $topic = Topic::where('id', $request->topic_id)
                      ->where('chapter_id', $request->chapter_id)
                      ->firstOrFail();

        $question->update([
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'topic_id' => $request->topic_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_option' => $request->correct_option,
            'solution' => $request->solution,
        ]);

        return redirect()->route('admin.question-bank.index')->with('success', 'Question updated.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.question-bank.index')->with('success', 'Question deleted.');
    }

    public function getTrades(Request $request)
    {
        $trades = Trade::where('trade_group_id', $request->trade_group_id)->get();
        return response()->json($trades);
    }

    public function getSubjects(Request $request)
    {
        $subjects = Subject::where('trade_id', $request->trade_id)->get();
        return response()->json($subjects);
    }

    public function getChapters(Request $request)
    {
        $chapters = Chapter::where('subject_id', $request->subject_id)->get();
        return response()->json($chapters);
    }

    public function getTopics(Request $request)
    {
        $topics = Topic::where('chapter_id', $request->chapter_id)->get();
        return response()->json($topics);
    }

    public function getTradess(Request $request)
    {
        $trades = Trade::where('trade_group_id', $request->trade_group_id)->get();
        return response()->json($trades);
    }

    public function getSubjectss(Request $request)
    {
        $subjects = Subject::where('trade_id', $request->trade_id)->get();
        return response()->json($subjects);
    }

    public function getChapterss(Request $request)
    {
        $chapters = Chapter::where('subject_id', $request->subject_id)->get();
        return response()->json($chapters);
    }

    public function getTopicss(Request $request)
    {
        $topics = Topic::where('chapter_id', $request->chapter_id)->get();
        return response()->json($topics);
    }
    public function bulkUpload()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.question-bank.bulk-upload', compact('tradeGroups'));
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'topic_id' => 'required|exists:topics,id',
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $hierarchy = [
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'topic_id' => $request->topic_id,
        ];

        try {
            Excel::import(new QuestionsImport($hierarchy), $request->file('file'));
            return redirect()->route('admin.question-bank.index')
                ->with('success', 'Questions imported successfully.');
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
}
