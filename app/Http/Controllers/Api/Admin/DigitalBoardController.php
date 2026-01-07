<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\class\Question;
use App\Http\Controllers\Controller;
use App\Models\QuestionSet;
use App\Models\QuestionTbl;
use App\Models\VirtualRoom;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DigitalBoardController extends Controller
{


    public function questions($setId)
    {
         if(!$setId) {
            return apiResponse(false, 'Set ID is required', [], 400);
        }
        $set = QuestionSet::find($setId);
        if (!$set) {
            return apiResponse(false, 'Set not found', [], 404);
        }

        // Fetch all sets with their related data
        // dd($set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? 0);

        $questions = QuestionTbl::with(['meta'])

            ->whereIn('question_tbls.id', function ($query) use ($setId) {
                $query->select('question_tbl_id')
                ->from('question_set_question_tbl')
                ->where('question_set_id', $setId);
            })
            // filter part end

            ->leftJoin('question_categories as topic', function ($join) {
                $join->on('topic.id', '=', 'question_tbls.category_id')
                    ->where('topic.depth_index', '=', 'topic');
            })
            ->leftJoin('question_categories as chapter', function ($join) {
                $join->on('chapter.id', '=', 'topic.parent_id')
                    ->where('chapter.depth_index', '=', 'chapter');
            })
            ->leftJoin('question_categories as subject', function ($join) {
                $join->on('subject.id', '=', 'chapter.parent_id')
                    ->where('subject.depth_index', '=', 'subject');
            })
            ->selectRaw('
                question_tbls.*,
                JSON_EXTRACT(question_tbls.question, "$.text.en") as question_text_en,
                JSON_EXTRACT(question_tbls.question, "$.text.hi") as question_text_hi,
                JSON_EXTRACT(question_tbls.options, "$[*].text.en") as options_text_en,
                JSON_EXTRACT(question_tbls.options, "$[*].text.hi") as options_text_hi,
                subject.name as subject_name,
                subject.id as subject_id,
                chapter.name as chapter_name,
                topic.name as topic_name
            ')

            // second filter part start
            // ->where('subject.id', '=', $set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? 0)
            ->get();


            $ques = $questions->map(function ($question) : array {
                $_newQuestion = [
                    'id' => $question->id,
                    // 'question_text_en' => json_decode($question->question_text_en) ?? $question->question_text_en,
                    // 'question_text_hi' => json_decode($question->question_text_hi) ?? $question->question_text_hi,
                    'subject_name' => $question->subject_name,
                    'subject_id' => $question->subject_id,
                    'chapter_name' => $question->chapter_name,
                    'formattedQuestion' => (new Question($question))->toObject(),
                ]; // Initialize an empty standard class object

                return $_newQuestion; // Corrected variable name
            });
    
        // Convert the questions to a more structured format
        
        return apiResponse(true, 'Questions fetched successfully', $ques);
    }


    public function createMirror(Request $request){
        Log::info($request->all());
        $validator = Validator::make($request->all(),[
            'setId'=> 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $room = VirtualRoom::create([
            'set_id' => $validated['setId'],
            'user_id' => auth()->user()->id,
            'room_token' => Str::random(5),
            'label' => "VR-".$request->set_id,
        ]);
        return apiResponse(true, 'Room created successfully', ['mirror_key' => $room->room_token??null]);
    }

    public function getMirrorKey($setId){
        $room = VirtualRoom::whereDate('created_at', today()->format('Y-m-d'))->where('set_id', $setId)->first();
        return apiResponse(true, 'Room created successfully', ['mirror_key' => $room->room_token??null]);
    }

    public function mirrorTheQuestion(Request $request){
        Log::info($request->all());
        // dd($request->all());
        $roomkey = $request->mirror_key;
        $topic = $roomkey."_current_question";
        // $topic = "test_topic";
        $message =  json_encode($request->all() ?? []);
        $qos = 0;
        $mqttService = new MqttService();
        $success = $mqttService->publish($topic, $message, $qos);
        return apiResponse(true, 'Questin rendered TO the mirror', []);
    }   
}
