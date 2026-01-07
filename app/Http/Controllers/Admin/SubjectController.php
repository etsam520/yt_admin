<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Trade;
use App\Models\TradeGroup;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('tradeGroup', 'trade')->paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.subjects.create', compact('tradeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'name' => 'required|string|max:255',
        ]);

        // Verify trade belongs to trade group
        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();

        Subject::create([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject added.');
    }

    public function edit(Subject $subject)
    {
        $tradeGroups = TradeGroup::all();
        $trades = Trade::where('trade_group_id', $subject->trade_group_id)->get();
        return view('admin.subjects.edit', compact('subject', 'tradeGroups', 'trades'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'trade_group_id' => 'required|exists:trade_groups,id',
            'trade_id' => 'required|exists:trades,id',
            'name' => 'required|string|max:255',
        ]);

        $trade = Trade::where('id', $request->trade_id)
                      ->where('trade_group_id', $request->trade_group_id)
                      ->firstOrFail();

        $subject->update([
            'name' => $request->name,
            'trade_group_id' => $request->trade_group_id,
            'trade_id' => $request->trade_id,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted.');
    }
}
