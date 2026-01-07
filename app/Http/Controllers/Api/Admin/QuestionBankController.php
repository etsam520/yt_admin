<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionTbl;
use App\Models\StreamContent;
use Illuminate\Http\Request;

class QuestionBankController extends Controller
{
    public function index(Request $request)
{
    $directory_id = $request->input('directory_id', null);
    $course_id = $request->input('course_id', null);
    $tradeNodeId = $request->input('trade_node_id', null);

    $question_tbl = StreamContent::where('stream_contents.type', 'question')
        ->select(
            'stream_contents.id',
            'stream_contents.type',
            'question_tbls.id as question_id',
            'question_tbls.*',
            'course_directories.name as directory_name',
            'courses.name as course_name',
            'trade_nodes.name as trade_node_name'
        )

        // CASE 1: Only trade_node_id is set
        ->when($tradeNodeId !== null && $course_id === null && $directory_id === null, function ($query) use ($tradeNodeId) {
            $query->where(function ($q) use ($tradeNodeId) {
                $q->where('stream_contents.trade_node_id', $tradeNodeId)
                    ->orWhereIn('stream_contents.course_directory_id', function ($subQuery) use ($tradeNodeId) {
                        $subQuery->select('id')
                            ->from('course_directories')
                            ->whereIn('course_id', function ($ssQuery) use ($tradeNodeId) {
                                $ssQuery->select('id')
                                    ->from('courses')
                                    ->where('trade_node_id', $tradeNodeId);
                            });
                    });
            });
        })

        // CASE 2: Only course_id is set
        ->when($course_id !== null && $directory_id === null && $tradeNodeId === null, function ($query) use ($course_id) {
            $query->whereIn('stream_contents.trade_node_id', function ($subQuery) use ($course_id) {
                $subQuery->select('trade_node_id')
                    ->from('courses')
                    ->where('id', $course_id);
            });
        })

        // CASE 3: Only directory_id is set
        ->when($directory_id !== null && $course_id === null && $tradeNodeId === null, function ($query) use ($directory_id) {
            $query->where('stream_contents.course_directory_id', $directory_id);
        })

        // CASE 4: Both course_id and directory_id are set
        ->when($course_id !== null && $directory_id !== null && $tradeNodeId === null, function ($query) use ($course_id, $directory_id) {
            $query->where('stream_contents.course_directory_id', $directory_id)
                ->whereIn('stream_contents.trade_node_id', function ($subQuery) use ($course_id) {
                    $subQuery->select('trade_node_id')
                        ->from('courses')
                        ->where('id', $course_id);
                });
        })

        // Joins
        ->leftJoin('question_tbls', 'stream_contents.target_id', '=', 'question_tbls.id')
        ->leftJoin('course_directories', 'stream_contents.course_directory_id', '=', 'course_directories.id')
        ->leftJoin('courses', 'course_directories.course_id', '=', 'courses.id')
        ->leftJoin('trade_nodes', function ($join) {
            $join->on('stream_contents.trade_node_id', '=', 'trade_nodes.id')
                ->orOn('courses.trade_node_id', '=', 'trade_nodes.id');
        })

        ->get();

    return response()->json($question_tbl);
}


    public function iXndex (Request $request)
    {
        $directory_id = $request->input('directory_id', null);
        $course_id = $request->input('course_id', null);
        $tradeNodeId = $request->input('trade_node_id', null);

           
        $question_tbl = StreamContent::where('type', 'question')
            ->select('stream_contents.*', 'question_tbls.*')
            ->when($tradeNodeId !== null && $course_id === null && $directory_id === null, function ($query) use ($tradeNodeId) {
                $query->where(function ($q) use ($tradeNodeId) {
                    $q->where('trade_node_id', $tradeNodeId)
                    ->orWhereIn('course_directory_id', function ($subQuery) use ($tradeNodeId) {
                        $subQuery->select('id')
                            ->from('course_directories')
                            ->whereIn('course_id', function ($ssQuery) use ($tradeNodeId) {
                                $ssQuery->select('id')
                                    ->from('courses')
                                    ->where('trade_node_id', $tradeNodeId);
                            });
                    });
                });
            })

            ->when($course_id !== null && $directory_id === null && $tradeNodeId === null, function ($query) use ($course_id) {
                $query->whereIn('trade_node_id', function ($subQuery) use ($course_id) {
                    $subQuery->select('trade_node_id')
                        ->from('courses')
                        ->where('id', $course_id);
                });
            })

            ->when($directory_id !== null && $course_id === null && $tradeNodeId === null, function ($query) use ($directory_id) {
                $query->where('course_directory_id', $directory_id);
            })

            ->when($course_id !== null && $directory_id !== null && $tradeNodeId === null, function ($query) use ($course_id, $directory_id) {
                $query->where('course_directory_id', $directory_id)
                    ->whereIn('trade_node_id', function ($subQuery) use ($course_id) {
                        $subQuery->select('trade_node_id')
                            ->from('courses')
                            ->where('id', $course_id);
                    });
            })


            ->get();

        return response()->json($question_tbl, 200);
    }

}
