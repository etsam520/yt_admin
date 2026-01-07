<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\Trade;
use App\Models\TradeGroup;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('tradeGroup', 'trade', 'subject')->paginate(10);
        return view('admin.chapters.index', compact('chapters'));
    }

    public function create()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.chapters.create', compact('tradeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        // Verify hierarchy
        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();
        $subject = Subject::where('id', $request->subject_id)
                          ->where('trade_id', $request->trade_id)
                          ->firstOrFail();

        Chapter::create([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
        ]);

        return redirect()->route('admin.chapters.index')->with('success', 'Chapter added.');
    }

    public function edit(Chapter $chapter)
    {
        $tradeGroups = TradeGroup::all();
        $trades = Trade::where('trade_group_id', $chapter->trade_group_id)->get();
        $subjects = Subject::where('trade_id', $chapter->trade_id)->get();
        return view('admin.chapters.edit', compact('chapter', 'tradeGroups', 'trades', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();
        $subject = Subject::where('id', $request->subject_id)
                          ->where('trade_id', $request->trade_id)
                          ->firstOrFail();

        $chapter->update([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
            'subject_id' => $request->subject_id,
        ]);

        return redirect()->route('admin.chapters.index')->with('success', 'Chapter updated.');
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('admin.chapters.index')->with('success', 'Chapter deleted.');
    }
}
