<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\Trade;
use App\Models\TradeGroup;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('tradeGroup', 'trade', 'subject', 'chapter')->paginate(10);
        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.topics.create', compact('tradeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'name' => 'required|string|max:255',
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

        Topic::create([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic added.');
    }

    public function edit(Topic $topic)
    {
        $tradeGroups = TradeGroup::all();
        $trades = Trade::where('trade_group_id', $topic->trade_group_id)->get();
        $subjects = Subject::where('trade_id', $topic->trade_id)->get();
        $chapters = Chapter::where('subject_id', $topic->subject_id)->get();
        return view('admin.topics.edit', compact('topic', 'tradeGroups', 'trades', 'subjects', 'chapters'));
    }

    public function update(Request $request, Topic $topic)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'name' => 'required|string|max:255',
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

        $topic->update([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
        ]);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted.');
    }
}
