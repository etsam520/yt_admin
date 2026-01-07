<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeGroup;
use Illuminate\Http\Request;

class TradeGroupController extends Controller
{
    public function index()
    {
        $tradeGroups = TradeGroup::paginate(10);
        return view('admin.trade-groups.index', compact('tradeGroups'));
    }

    public function create()
    {
        return view('admin.trade-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        TradeGroup::create($request->all());
        return redirect()->route('admin.trade-groups.index')->with('success', 'Trade Group added.');
    }

    public function edit(TradeGroup $tradeGroup)
    {
        return view('admin.trade-groups.edit', compact('tradeGroup'));
    }

    public function update(Request $request, TradeGroup $tradeGroup)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $tradeGroup->update($request->all());
        return redirect()->route('admin.trade-groups.index')->with('success', 'Trade Group updated.');
    }

    public function destroy(TradeGroup $tradeGroup)
    {
        $tradeGroup->delete();
        return redirect()->route('admin.trade-groups.index')->with('success', 'Trade Group deleted.');
    }
}
