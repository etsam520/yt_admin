<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\TradeGroup;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function index()
    {
        $trades = Trade::with('tradeGroup')->paginate(10);
        return view('admin.trades.index', compact('trades'));
    }

    public function create()
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.trades.create', compact('tradeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trade_group_id' => 'required|exists:trade_groups,id',
        ]);
        Trade::create($request->all());
        return redirect()->route('admin.trades.index')->with('success', 'Trade added.');
    }

    public function edit(Trade $trade)
    {
        $tradeGroups = TradeGroup::all();
        return view('admin.trades.edit', compact('trade', 'tradeGroups'));
    }

    public function update(Request $request, Trade $trade)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trade_group_id' => 'required|exists:trade_groups,id',
        ]);
        $trade->update($request->all());
        return redirect()->route('admin.trades.index')->with('success', 'Trade updated.');
    }

    public function destroy(Trade $trade)
    {
        $trade->delete();
        return redirect()->route('admin.trades.index')->with('success', 'Trade deleted.');
    }
}
