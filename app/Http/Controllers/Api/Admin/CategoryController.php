<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

    public function index()
    {
        $tree = CategoryController::getHierarchy();
        return apiResponse(true, 'success', $tree);
    }

    public function getCategoryesByDepthIndex(Request $request)
    {
        $depthIndex = $request->input('depth_index');
        $parentId = $request->input('parent_id');
        if (!$depthIndex) {
            return apiResponse(false, 'Depth index is required', [], 400);
        }
        if($parentId){
            $categories = QuestionCategory::with('children')->where('depth_index', $depthIndex)->where('parent_id', $parentId)->get();
            return apiResponse(true, 'Categories fetched successfully', $categories);
        }
        $categories = QuestionCategory::with('children')->where('depth_index', $depthIndex)->get();

        return apiResponse(true, 'Categories fetched successfully', $categories);
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'depth_index' => 'required|in:subject,chapter,topic',
            'parent_id' => 'nullable|exists:question_categories,id',
        ]);

        $category = QuestionCategory::create($validatedData);

        return apiResponse(true, 'Category created successfully', $category);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:question_categories,name,' . $id,
            'slug' => 'required|string|max:255|unique:question_categories,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'depth_index' => 'required|in:subject,chapter,topic',
            'parent_id' => 'nullable|exists:question_categories,id',
        ]);

        $category = QuestionCategory::findOrFail($id);
        $category->update($validated);

        return apiResponse(true, 'Category updated successfully', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = QuestionCategory::select('*')
        ->selectSub(function ($query) use ($id) {
                $query->from('question_categories')
                        ->where('parent_id', $id)
                        ->selectRaw('COUNT(*)');
                }, 'children_count')
        ->findOrFail($id);

        if ($category->children_count > 0) {
        return apiResponse(false, 'Cannot delete category with subcategories', 400);
        }

        $category->delete();

        return apiResponse(true, 'Category deleted successfully');
    }

    /**
     * Get the hierarchy of categories.
     */
    public static function getHierarchy() : array
    {
        $categories = QuestionCategory::all(); // single query

        // Step 1: Build a flat map of all categories
        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category->id] = (object)[
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'depth_index' => $category->depth_index,
                'parent_id' => $category->parent_id,
                'children' => [],
            ];
        }

        // Step 2: Attach children to parents using only one pass
        $tree = [];
        foreach ($categoryMap as $category) {
            if ($category->parent_id && isset($categoryMap[$category->parent_id])) {
                $categoryMap[$category->parent_id]->children[] = $category;
            } else {
                $tree[] = $category; // top-level category (likely 'subject')
            }
        }

        return $tree;
    }
}
