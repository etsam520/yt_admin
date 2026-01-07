<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\select;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'trade_node_id' => $request->query('trade_node'),
            'status' => $request->query('status'),
            'search' => $request->query('search')
        ];

        $courses = \App\Models\Course::query()
            ->when($filters['trade_node_id'], function ($query, $tradeNodeId) {
                $query->where('trade_node_id', $tradeNodeId);
            })
            ->when($filters['status'], function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->leftJoin('trade_nodes', 'courses.trade_node_id', '=', 'trade_nodes.id')
            ->select([
                'courses.*',
                'trade_nodes.name as trade_node_name',
                // Subquery for one top-level directory (e.g., the one with the lowest ID)
                DB::raw("(
            SELECT name
            FROM course_directories
            WHERE course_directories.course_id = courses.id
              AND course_directories.parent_id IS NULL
            ORDER BY id ASC
            LIMIT 1
        ) as directory_name"),
                DB::raw("(
            SELECT id
            FROM course_directories
            WHERE course_directories.course_id = courses.id
              AND course_directories.parent_id IS NULL
            ORDER BY id ASC
            LIMIT 1
        ) as directory_id")
            ])
            ->latest()
            ->get();

        $courses->transform(function ($course) {
            $course->makeHidden(['updated_at']);
            $course->image = $course->image ? asset($course->image) : null;
            return $course;
        });
        return response()->json($courses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'required|string|unique:courses,slug',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'trade_node_id' => 'required|exists:trade_nodes,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/all'), $imageName);
            // Save relative path or just filename
            $validated['image'] = 'uploads/all/' . $imageName;
        }
        $course = \App\Models\Course::create($validated);

        return response()->json($course, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = \App\Models\Course::select('courses.*')
            ->selectRaw('trade_nodes.name as trade_node_name')
            ->leftJoin('trade_nodes', 'courses.trade_node_id', '=', 'trade_nodes.id')
            ->findOrFail($id);
        $course->makeHidden(['updated_at', 'created_at']);
        $course->image = $course->image ? asset($course->image) : null;
        return response()->json($course);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = \App\Models\Course::findOrFail($id);
        $course->image = $course->image ? asset('storage/' . $course->image) : null;
        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = \App\Models\Course::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'trade_node_id' => 'required|exists:trade_nodes,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/all'), $imageName);
            // Save relative path or just filename
            $validated['image'] = 'uploads/all/' . $imageName;
        }

        $course->update($validated);

        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = \App\Models\Course::findOrFail($id);
        if ($course->image) {
            // Optionally delete the image file from storage
            Storage::delete(public_path($course->image));
        }
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully'], 204); // 204 No Content
    }
}
