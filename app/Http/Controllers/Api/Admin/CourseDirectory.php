<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseDirectory extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $course_id)
    {
        $directories = \App\Models\CourseDirectory::where('course_id', $course_id)
            ->select('id', 'name', 'parent_id', 'status')->get();
        return response()->json($directories);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:course_directories,id', // Allow null for root directories
        ]);

        $directory = \App\Models\CourseDirectory::create($validated);
        return response()->json($directory, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $directory = \App\Models\CourseDirectory::findOrFail($id);
        return response()->json($directory); // 200 OK
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:course_directories,id', // Allow null for root directories
            'id' => 'required|exists:course_directories,id', // Ensure the ID exists
        ]);

        $directory = \App\Models\CourseDirectory::findOrFail($id);
        $directory->update($validated);

        return response()->json($directory, 200); // 200 OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $directory = \App\Models\CourseDirectory::findOrFail($id);
        // Check if the node has children
        if ($directory->children()->exists()) {
            return response()->json(['message' => 'Cannot delete a node with children'], 400);
        }
        $directory->delete();

        return response()->json(['message' => 'Directory deleted successfully'], 200); // 200 OK
    }
}
