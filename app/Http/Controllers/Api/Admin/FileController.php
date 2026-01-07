<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StreamContent;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $dir, string $dir_id, Request $request)
    {
        $f = [];
        $f['directory'] = $dir; // directory
        $f['directory_id'] = $dir_id; // directory id

        $data = StreamContent::where('course_directory_id', $dir_id)
            ->where(function ($query) {
                $query->where('stream_contents.type', 'video')
                    ->orWhere('stream_contents.type', 'pdf')
                    ->orWhere('stream_contents.type', 'question')
                    ->orWhere('stream_contents.type', 'ppt');
            })
            ->leftJoin('video_tbls', function ($join) {
                $join->on('stream_contents.target_id', '=', 'video_tbls.id')
                    ->where('stream_contents.target_table', 'video_tbls');
            })
            ->leftJoin('pdf_tbls', function ($join) {
                $join->on('stream_contents.target_id', '=', 'pdf_tbls.id')
                    ->where('stream_contents.target_table', 'pdf_tbls');
            })
            ->leftJoin('question_tbls', function ($join) {
                $join->on('stream_contents.target_id', '=', 'question_tbls.id')
                    ->where('stream_contents.target_table', 'question_tbls');
            })
            ->leftJoin('ppt_tbls', function ($join) {
                $join->on('stream_contents.target_id', '=', 'ppt_tbls.id')
                    ->where('stream_contents.target_table', 'ppt_tbls');
            })
            ->select('stream_contents.*', 'video_tbls.*', 'pdf_tbls.*', 'question_tbls.*', 'ppt_tbls.*')
            ->get();

        $f['contents'] = $data;
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
