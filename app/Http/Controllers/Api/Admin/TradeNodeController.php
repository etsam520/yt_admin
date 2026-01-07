<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeNode;
use Illuminate\Http\Request;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class TradeNodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() :JsonResponse
    {
        return response()->json(TradeNode::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:trade_nodes,id', // parentId will be null for base nodes
        ]);

        $node = TradeNode::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id ?? null, // Map 'parentId' from frontend to 'parent_id' in DB
            'slug' => Str::slug($request->name), // Generate slug from name
        ]);

        return response()->json($node, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(TradeNode $node) : JsonResponse
    {
        return response()->json($node);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, String $id) : JsonResponse
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:trade_nodes,id', // Allow updating parent_id
        ]);
        $node = TradeNode::where('id', $id)->update(
            [
                'name' => $request->name,
                'parent_id' => $request->parent_id ?? null, // Map 'parentId' from frontend to 'parent_id' in DB
                'slug' => Str::slug($request->name), // Update slug from name
            ]
        );
        if (!$node) {
            return response()->json(['message' => 'Trade Update Failied'],  500);
        }
        // Fetch the updated node
        $node = TradeNode::select('id', 'name', 'parent_id')->findOrFail($id);

        return response()->json($node);
    }

    public function destroy(String $id) : JsonResponse
    {
        $node = TradeNode::findOrFail($id);

        // Check if the node has children
        // if ($node->children()->exists()) {
        //     return response()->json(['message' => 'Cannot delete a node with children'], 400);
        // }

        // Delete the node\
        $node->delete();
        return response()->json(['message' => 'Trade Node deleted successfully']);
    }
}
