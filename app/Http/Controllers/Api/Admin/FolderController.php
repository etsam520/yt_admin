<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Foder;
use App\Models\Folder;
use App\Models\QuestionSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'part_of' => 'nullable|in:question_set,questions',
        ]);
        
        if ($validator->fails()) {
            return apiResponse(false, 'Validation Error', $validator->errors()->first(), 422);
        }
        $validated = $validator->validated();
        $validated['created_by'] = auth()->id();
     
        if(isset($validated['parent_id'])) {
            $validated['part_of'] = null; // if has parent, part_of should be null
            $parent = Folder::find($validated['parent_id']);
            if ($parent) {
                $validated['level'] = $parent->level + 1;
            } else {
                return apiResponse(false, 'Parent folder not found', null, 404);
            }
        } else {
            $validated['level'] = 0;
        }
        // return apiResponse(true, 'Validation Passed', $validated);
        $folder = Folder::create($validated);
        return apiResponse(true, 'Folder created successfully', $folder, 201);
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'path' => 'sometimes|nullable|string|max:255',
            'level' => 'sometimes|nullable|integer',
            'parent_id' => 'sometimes|nullable|exists:folders,id',
        ]);

        $folder = Folder::find($id);
        if (!$folder) {
            return apiResponse(false, 'Folder not found', null, 404);
        }

        $folder->update($validated);

        // compute level difference if provided
        $newLevel = $validated['level'] ?? $folder->level;
        $oldLevel = $folder->getOriginal('level');
        $diff = $newLevel - $oldLevel;

        // if level changed, update all descendants
        if ($diff !== 0) {
            DB::statement("
                WITH RECURSIVE descendants AS (
                    SELECT id, parent_id, level FROM folders WHERE parent_id = ?
                    UNION ALL
                    SELECT f.id, f.parent_id, f.level
                    FROM folders f
                    INNER JOIN descendants d ON f.parent_id = d.id
                )
                UPDATE folders
                JOIN descendants ON folders.id = descendants.id
                SET folders.level = folders.level + ?
            ", [$folder->id, $diff]);
        }

        return apiResponse(true, 'Folder and descendants updated successfully', $folder);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $folder = Folder::find($id);
        if (!$folder) {
            return apiResponse(false, 'Folder not found', null, 404);
        }
        $folder->delete();
        return apiResponse(true, 'Folder deleted successfully');
    }


    public function currentFolder(Request $request, string $id)
    {
        $folder = Folder::findOrFail($id);
        return apiResponse(true, 'Folder retrieved successfully', [
            'folder' => $folder,
        ]);
    }

    public function folderBreadcrumbs($folderId)
    {
        $breadcrumbs = [];
        $current = Folder::find($folderId);
        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'parent_id' => $current->parent_id,
            ]);
            $current = $current->parent;
        }
        return $breadcrumbs;
    }


    public function setFolder(Request $request)
    {

        $request->validate([
            'parent_id' => 'nullable|exists:folders,id', 
        ]);
        $folderId = $request->input('parent_id', null);

        if(!$folderId) {
            $folders = Folder::where('part_of', 'question_set')->where('created_by', auth()->id())->orderBy('name', 'asc')->get();
        } else {
            $folders = Folder::where('parent_id', $folderId)->where('created_by', auth()->id())->orderBy('name', 'asc')->get();
        }

        
       

        return apiResponse(true, 'Folders retrieved successfully', [
            'folders' => $folders,
            'breadcrumbs' => $folderId !== null || $folderId !== '' ? $this->folderBreadcrumbs($folderId) : [],
        ]);
    }

    public function XsetFolder(Request $request)
    {

        $request->validate([
            'folder_id' => 'nullable|exists:folders,id', 
        ]);
        $folderId = $request->input('folder_id', null);
        if(!$folderId) {
            $folders = Folder::where('part_of', 'question_set')->orderBy('name', 'asc')->get();
            $sets = QuestionSet::with(['creator', 'meta', 'folders'])
            ->leftJoin('organizations as organization', function ($join) {
                $join->on('organization.id', '=', 'question_sets.organization_id');
            })
            ->select('question_sets.*', 'organization.name as organization_name')
            ->orderBy('question_sets.created_at', 'desc')
            ->get()
            ->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'description' => $set->description,
                    'is_active' => $set->is_active,
                    'is_public' => $set->is_public,
                    'password' => $set->password,
                    'created_by' => $set->created_by,
                    'category_id' => $set->category_id,
                    'category_depth_index' => $set->category_depth_index,
                    'organization_id' => $set->organization_id,
                    'organization_name' => $set->organization_name,
                    'created_at' => $set->created_at,
                    'updated_at' => $set->updated_at,
                    'creator' => $set->creator,
                    'meta' => $set->meta,
                ];
            });
        } else {
            $folders = Folder::where('parent_id', $folderId)->orderBy('name', 'asc')->get();
            $sets = QuestionSet::whereHas('folders', function ($query) use ($folderId) {
                $query->where('folders.id', $folderId);
            })
            ->with(['creator', 'meta', 'folders'])
            ->leftJoin('organizations as organization', function ($join) {
                $join->on('organization.id', '=', 'question_sets.organization_id');
            })
            ->select('question_sets.*', 'organization.name as organization_name')
            ->orderBy('question_sets.created_at', 'desc')
            ->get()
            ->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name'=>$set->name,
                    'description'=>$set->description,
                    'is_active'=>$set->is_active,
                    'is_public'=>$set->is_public,
                    'password'=>$set->password,
                    'created_by'=>$set->created_by,
                    'category_id'=>$set->category_id,
                    'category_depth_index'=>$set->category_depth_index,
                    'organization_id'=>$set->organization_id,
                    'organization_name'=>$set->organization_name,
                    'created_at'=>$set->created_at,
                    'updated_at'=>$set->updated_at,
                    'creator'=>$set->creator,
                    'meta'=>$set->meta,
                ];
            });
        }
        
       

        return apiResponse(true, 'Folders retrieved successfully', [
            'folders' => $folders,
            'sets' => $sets
        ]);
    }
}
