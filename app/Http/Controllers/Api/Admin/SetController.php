<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\class\Question;
use App\Http\Controllers\Controller;
use App\Models\QuestionMeta;
use App\Models\QuestionSet;
use App\Models\QuestionSetMeta;
use App\Models\QuestionTbl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use stdClass;

class SetController extends Controller
{
    /*public function index() // WITH SUBJECT CHAPTER AND ORGANIZAION AND LESSON
    {
        $sets = QuestionSet::with(['creator', 'meta',])
            ->join('question_categories as topic', function ($join) {
                $join->on('topic.id', '=', 'question_sets.category_id')
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
            ->leftJoin('organizations as organization', function ($join) {
                $join->on('organization.id', '=', 'question_sets.organization_id');
            })
            ->selectRaw('
                question_sets.*,
                subject.name as subject_name,subject.id as subject_id,
                chapter.name as chapter_name,chapter.id as chapter_id,
                topic.name as topic_name,topic.id as topic_id,
                organization.name as organization_name,organization.id as organization_id,
                (
                    SELECT COUNT(*)
                    FROM question_set_question_tbl
                    LEFT JOIN question_tbls AS question ON question.id = question_set_question_tbl.question_tbl_id
                    WHERE question_set_question_tbl.question_set_id = question_sets.id
                ) AS question_count
            ')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'description' => $set->description,
                    'is_active' => $set->is_active,
                    'is_public' => $set->is_public,
                    'creator' => $set->creator ? [
                        'id' => $set->creator->id,
                        'name' => $set->creator->name,
                    ] : null,
                    'created_at' => $set->created_at,
                    'subject' => [
                        'id' => $set->subject_id,
                        'name' => $set->subject_name,
                    ],
                    // 'chapter_name' => $set->chapter_name,
                    // 'topic_name' => $set->topic_name,
                    'organization' => [
                        'id' => $set->organization_id,
                        'name' => $set->organization_name,
                    ],
                    'question_count' => $set->question_count,
                    'meta' => $set->meta->map(function ($meta) {
                        return [
                            'meta_key' => $meta->meta_key,
                            'meta_value' => $meta->meta_value, 
                            ];
                    })
                ];
            });
        return apiResponse(true, 'Sets fetched successfully', $sets);
            
        // return response()->json($sets);
    }*/

    public function index(Request $request)
    {
        $folderId = $request->query('folder_id', null);
        $userId = auth()->user()->id;
        // if($folderId == "_blank")
        if($folderId) {
           $sets = QuestionSet::leftJoin('folder_question_set as fqs', 'fqs.question_set_id', '=', 'question_sets.id')
            ->leftJoin('organizations as organization', 'organization.id', '=', 'question_sets.organization_id')
            ->selectRaw('
                question_sets.*,
                fqs.folder_id as folder_id,
                organization.name as organization_name,
                organization.id as organization_id,
                (
                    SELECT COUNT(*)
                    FROM question_set_question_tbl
                    LEFT JOIN question_tbls AS question 
                        ON question.id = question_set_question_tbl.question_tbl_id
                    WHERE question_set_question_tbl.question_set_id = question_sets.id
                ) AS question_count
            ')
            ->when($folderId === '_blank', function ($query) {
                // Sets NOT in any folder
                $query->whereNull('fqs.folder_id');
            })
            ->when($folderId !== '_blank', function ($query) use ($folderId, $userId) {
                // Sets in selected folder
                $query->where('fqs.folder_id', $folderId)
                ->join('folders as fd', function ($join) use ($folderId, $userId) {
                    $join->on('fd.id', '=', 'fqs.folder_id')
                         ->where('fd.created_by', '=', $userId);
                });
            })
            ->where('question_sets.created_by', $userId)
            ->orderBy('question_sets.created_at', 'desc')
            ->get()
            ->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'description' => $set->description,
                    'is_active' => $set->is_active,
                    'is_public' => $set->is_public,
                    'creator' => $set->creator ? [
                        'id' => $set->creator->id,
                        'name' => $set->creator->name,
                    ] : null,
                    'created_at' => $set->created_at,
                    'subject' => [
                        'id' => $set->subject_id,
                        'name' => $set->subject_name,
                    ],
                    // 'chapter_name' => $set->chapter_name,
                    // 'topic_name' => $set->topic_name,
                    'organization' => [
                        'id' => $set->organization_id,
                        'name' => $set->organization_name,
                    ],
                    'question_count' => $set->question_count,
                    'meta' => $set->meta->map(function ($meta) {
                        return [
                            'meta_key' => $meta->meta_key,
                            'meta_value' => $meta->meta_value, 
                            ];
                    })
                ];
            });
            return apiResponse(true, 'Sets fetched successfully', $sets);
        }
        $sets = QuestionSet::with(['creator', 'meta',])
            ->leftJoin('organizations as organization', function ($join) {
                $join->on('organization.id', '=', 'question_sets.organization_id');
            })
            ->selectRaw('
                question_sets.*,
                organization.name as organization_name,organization.id as organization_id,
                (
                    SELECT COUNT(*)
                    FROM question_set_question_tbl
                    LEFT JOIN question_tbls AS question ON question.id = question_set_question_tbl.question_tbl_id
                    WHERE question_set_question_tbl.question_set_id = question_sets.id
                ) AS question_count
            ')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'description' => $set->description,
                    'is_active' => $set->is_active,
                    'is_public' => $set->is_public,
                    'creator' => $set->creator ? [
                        'id' => $set->creator->id,
                        'name' => $set->creator->name,
                    ] : null,
                    'created_at' => $set->created_at,
                    'subject' => [
                        'id' => $set->subject_id,
                        'name' => $set->subject_name,
                    ],
                    // 'chapter_name' => $set->chapter_name,
                    // 'topic_name' => $set->topic_name,
                    'organization' => [
                        'id' => $set->organization_id,
                        'name' => $set->organization_name,
                    ],
                    'question_count' => $set->question_count,
                    'meta' => $set->meta->map(function ($meta) {
                        return [
                            'meta_key' => $meta->meta_key,
                            'meta_value' => $meta->meta_value, 
                            ];
                    })
                ];
            });
        return apiResponse(true, 'Sets fetched successfully', $sets);
            
        // return response()->json($sets);
    }

    



    public function store(Request $request)
    {
        try {

          
            $validator = Validator::make($request->all(), [
                'folder_id' => 'sometimes|exists:folders,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'required|boolean',
                'is_public' => 'required|boolean',
                'password' => 'required|string|min:8',
                'organization' => 'required|integer|exists:organizations,id',
                
                // 'subject_id' => 'required|integer|exists:question_categories,id',
                // 'chapter_id' => 'required|integer|exists:question_categories,id',
                // 'topic_id' => 'required|integer|exists:question_categories,id',
                "total_marks" => 'required|integer|min:1',
                "negative_mark" => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return apiResponse(false, 'Validation failed', $validator->errors(), 422);
               
            }
            DB::beginTransaction();

            $validated = $validator->validated();

            $set = QuestionSet::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'],
                'is_public' => $validated['is_public'],
                'password' => bcrypt($validated['password']),
                'created_by' => Auth::user()->id,
                'organization_id' => $validated['organization'],
                // 'category_id' => $validated['topic_id'], // assuming topic is the deepest category
                // 'category_depth_index' => 'topic',  
            ]);
            if(isset($validated['folder_id'])){
                $set->folders()->attach($validated['folder_id']);
            }
            $meta = [
                // ['question_set_id' => $set->id, 'meta_key' => 'subject_id', 'meta_value' => $validated['subject_id']],
                // ['question_set_id' => $set->id, 'meta_key' => 'chapter_id', 'meta_value' => $validated['chapter_id']],
                // ['question_set_id' => $set->id, 'meta_key' => 'topic_id', 'meta_value' => $validated['topic_id']],
                ['question_set_id' => $set->id, 'meta_key' => 'organization_id', 'meta_value' => $validated['organization']],
                ['question_set_id' => $set->id, 'meta_key' => 'total_marks', 'meta_value' => $validated['total_marks']],
                ['question_set_id' => $set->id, 'meta_key' => 'negative_mark', 'meta_value' => $validated['negative_mark']],
            ];
            QuestionSetMeta::insert($meta);
            DB::commit();
            return apiResponse(true, 'Set created successfully', $set);
        } catch (\Throwable $e) {
            Log::error('Error creating set: ' . $e->getMessage());
            DB::rollBack();
           return apiResponse(false, 'An error occurred while creating the set', ['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $set = QuestionSet::with(['creator', 'course', 'tradeNode'])->findOrFail($id);
        return response()->json($set);
    }

    public function update(Request $request, $id)
    {
        $set = QuestionSet::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'password' => 'nullable|string|min:6',
            // 'course_id' => 'nullable|exists:courses,id',
            'trade_node_id' => 'sometimes|exists:trade_nodes,id',
            "total_marks" => 'required|integer|min:1',
            "negative_mark" => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only([
            'name', 'description', 'is_active', 'is_public', 'course_id', 'trade_node_id'
        ]);

        if ($request->has('password')  && $request->password != null) {
            $data['password'] = bcrypt($request->password);
        }

        $set->update($data);

        $meta = [
            ['question_set_id' => $set->id, 'meta_key' => 'total_marks', 'meta_value' => $request->total_marks],
            ['question_set_id' => $set->id, 'meta_key' => 'negative_mark', 'meta_value' => $request->negative_mark],
        ]
        ;
        QuestionSetMeta::upsert($meta, ['question_set_id', 'meta_key'], ['meta_value']);
        return response()->json($set);
    }


    public function destroy($id)
    {
        $set = QuestionSet::findOrFail($id);
        $set->delete();
        return response()->json(null, 204);
    }

   
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:sets,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $sets = QuestionSet::whereIn('id', $request->ids)->get();

        switch ($request->action) {
            case 'activate':
                QuestionSet::whereIn('id', $request->ids)->update(['is_active' => true]);
                break;
            case 'deactivate':
                QuestionSet::whereIn('id', $request->ids)->update(['is_active' => false]);
                break;
            case 'delete':
                QuestionSet::whereIn('id', $request->ids)->delete();
                break;
        }

        return response()->json(['message' => 'Bulk action completed successfully']);
    }

     //  Route::get('questionsAvalableForSet/{setId}', 'App\Http\Controllers\Api\Admin\SetController@questionsAvalableForSet');
    // Route::get('questionsStoredToSet/{setId}', 'App\Http\Controllers\Api\Admin\SetController@questionsStoredToSet');
    // Route::post('questionsStoreToSet/{setId}', 'App\Http\Controllers\Api\Admin\SetController@questionsStoreToSet');

    public function questionsAvalableForSet(Request $request)
    {
        $setId = $request->input('setId', null);
        $filters = $request->input('filters', []);
        // $organization = $filters['organization'] ?? null;
        $organization =  null;
        $group = $filters['group'] ?? null;
        $subject = $filters['subject'] ?? null;
        $chapter = $filters['chapter'] ?? null;
        $topic = $filters['topic'] ?? null;
        $global = $filters['global'] ?? null;

       
    //     return apiResponse(false, ($isTeacherQuestion ? 
    // "this is a teacher question" : "this is not a teacher question"), [], 501);

        //Question beloings to  both = 0, teacher = 1, admin = 2
        $qbt = auth()->user()->role == 'admin' ? 2: 0;
        $isTeacherQuestion = ($group == 'teacher_question' ? true : false);
        if($isTeacherQuestion):
            $group = "npq"  ;
            $qbt = 1;
        elseif(!$isTeacherQuestion && $group == 'npq'):
            $qbt = 2;
        endif;
        

        $user = Auth::user();
        $admin = $qbt == 0 ? User::where('role', 'admin')->first() : $user;
        // Log::info('Filters Data: ', $filters);

        if (!$setId) {
            return apiResponse(false, 'Set ID is required', [], 400);
        }
        $set = QuestionSet::find($setId);
        if (!$set) {
            return apiResponse(false, 'Set not found', [], 404);
        }

        // fallback subject from set meta if user didn't provide filter
        $setSubject = $set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? null;

        $query = QuestionTbl::with(['meta'])
            ->whereNotIn('question_tbls.id', function ($query) use ($setId) {
                $query->select('question_tbl_id')
                    ->from('question_set_question_tbl')
                    ->where('question_set_id', $setId);
            })
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
            ->leftJoin('question_metas as group_meta', function ($join)  {
                $join->on('group_meta.question_id', '=', 'question_tbls.id')
                    ->where('group_meta.meta_key', '=', 'group');
            })
            ->leftJoin('question_metas as meta_organisation', function ($join)  {
                $join->on('meta_organisation.question_id', '=', 'question_tbls.id')
                    ->where('meta_organisation.meta_key', '=', 'organisation_id');
            })
            // ->where(function ($q) use ($group) {
            //     if ($group) {
            //         $q->where('group_name', '=', $group); 
            //     }
            // })
            ->selectRaw('
                question_tbls.*,
                JSON_EXTRACT(question_tbls.question, "$.text.en") as question_text_en,
                JSON_EXTRACT(question_tbls.question, "$.text.hi") as question_text_hi,
                JSON_EXTRACT(question_tbls.options, "$[*].text.en") as options_text_en,
                JSON_EXTRACT(question_tbls.options, "$[*].text.hi") as options_text_hi,
                subject.name as subject_name,
                subject.id as subject_id,
                chapter.name as chapter_name,
                topic.name as topic_name,
                IFNULL(group_meta.meta_value, "pyq") as group_name,
                (
                    SELECT qm.meta_value 
                    FROM question_metas qm
                    WHERE qm.question_id = question_tbls.id 
                    AND qm.meta_key = "ca_date" 
                    LIMIT 1
                ) as ca_date,
                (select name from organizations where id = meta_organisation.meta_value limit 1) as organisation_name

            ')

            ->having('group_name', '=', $group)

            // apply filters only when provided (nullable-safe)
            ->when($organization, function ($q, $organization) {
                // assuming question_tbls has organization_id
                return $q->where('question_tbls.organization_id', $organization);
            })
            ->when($subject ?? $setSubject, function ($q, $val) {
                return $q->where('subject.id', $val);
            })
            ->when($chapter, function ($q, $chapter) {
                return $q->where('chapter.id', $chapter);
            })
            ->when($topic, function ($q, $topic) {
                return $q->where('topic.id', $topic);
            })
            ->when($global, function ($q, $global) {
                $like = '%' . str_replace('%', '\\%', $global) . '%';
                return $q->whereRaw('(JSON_UNQUOTE(JSON_EXTRACT(question_tbls.question, "$.text.en")) LIKE ? OR JSON_UNQUOTE(JSON_EXTRACT(question_tbls.question, "$.text.hi")) LIKE ?)', [$like, $like]);
            });

        $questions = $query->when($qbt == 2 && $user->role == 'admin', function ($query) use ($user) {
                $query->where('question_tbls.created_by', '!=', $user->id);
            })
            ->when($qbt == 0  && $admin != null, function ($query) use ($admin, $user) {
                $query->where(function($q) use ($admin, $user) {
                    $q->whereIn('question_tbls.created_by', [$admin->id, $user->id]);
                });
            })
            ->when($qbt == 1 && $user->role !== 'admin', function ($query) use ($user) {
                $query->where('question_tbls.created_by', $user->id);
            })
            ->when($qbt == 2 , function ($query) use ($admin){
                $query->where('question_tbls.created_by', $admin->id);
            })
        ->get();

        $ques = $questions->map(function ($question) : object {
            $_newQuestion = new stdClass();
            $_newQuestion->id = $question->id;
            $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
            $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
            $_newQuestion->organisation_name = $question->organisation_name?? null;
            $_newQuestion->subject_name = $question->subject_name;
            $_newQuestion->subject_id = $question->subject_id;
            $_newQuestion->chapter_name = $question->chapter_name;
            $_newQuestion->topic_name = $question->topic_name;
            $_newQuestion->group_name = $question->group_name;
            $_newQuestion->ca_date = $question->ca_date;
            $_newQuestion->formattedQuestion = (new Question($question))->toObject();

            return $_newQuestion;
        });


        return apiResponse(true, 'Questions fetched successfully', $ques);
    }

    public function questionsOfSet($setId)
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
            ->leftJoin('question_metas as group_meta', function ($join)  {
                $join->on('group_meta.question_id', '=', 'question_tbls.id')
                    ->where('group_meta.meta_key', '=', 'group');
            })
            ->leftJoin('question_metas as meta_organisation', function ($join)  {
                $join->on('meta_organisation.question_id', '=', 'question_tbls.id')
                    ->where('meta_organisation.meta_key', '=', 'organisation_id');
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
                topic.name as topic_name,
                IFNULL(group_meta.meta_value, "pyq") as group_name,
                (
                    SELECT qm.meta_value 
                    FROM question_metas qm
                    WHERE qm.question_id = question_tbls.id 
                    AND qm.meta_key = "ca_date" 
                    LIMIT 1
                ) as ca_date,
                (select name from organizations where id = meta_organisation.meta_value limit 1) as organisation_name
            ')

            // second filter part start
            // ->where('subject.id', '=', $set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? 0)
            ->get();
            // dd($questions);

            $ques = $questions->map(function ($question) : object {
                $_newQuestion = new stdClass(); // Initialize an empty standard class object
                $_newQuestion->id = $question->id;
                $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
                $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
                 $_newQuestion->organisation_name = $question->organisation_name?? null;
                $_newQuestion->subject_name = $question->subject_name;
                $_newQuestion->subject_id = $question->subject_id;
                $_newQuestion->chapter_name = $question->chapter_name;
                $_newQuestion->topic_name = $question->topic_name;
                $_newQuestion->formattedQuestion = (new Question($question))->toObject();

                return $_newQuestion; // Corrected variable name
            });
    
        // Convert the questions to a more structured format
        
        return apiResponse(true, 'Questions fetched successfully', $ques);
    }

    public function questionsStoreToSet(Request $request, $setId)
    {
        if(!$setId) {
            return apiResponse(false, 'Set ID is required', [], 400);
        }
        $set = QuestionSet::find($setId);
        if (!$set) {
            return apiResponse(false, 'Set not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'questionIds' => 'required|array',
            'questionIds.*' => 'exists:question_tbls,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $questions = $request->input('questionIds');

        foreach ($questions as $questionId) {
            DB::table('question_set_question_tbl')->insert([
                'question_set_id' => $setId,
                'question_tbl_id' => $questionId,
            ]);
        }

        return apiResponse(true, 'Questions added to set successfully', []);
    }

    public function removeQuestionsFromSet($setId)
    {
        if(!$setId) {
            return apiResponse(false, 'Set ID is required', [], 400);
        }
        $set = QuestionSet::find($setId);
        if (!$set) {
            return apiResponse(false, 'Set not found', [], 404);
        }

        $validator = Validator::make(request()->all(), [
            'questions' => 'required|array',
            'questions.*' => 'exists:question_tbls,id',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $questions = request()->input('questions');

        DB::table('question_set_question_tbl')
            ->where('question_set_id', $setId)
            ->whereIn('question_tbl_id', $questions)
            ->delete();

        return apiResponse(true, 'Questions removed from set successfully', []);
    }


    /*
    public function questionsAvalableForSet(Request $request)
    {
        // Log::info('Request Data: ', $request->all());
        // 2025-09-06 09:14:05] local.INFO: Request Data:  {"setId":9,"filters":{"global":"","organization":13,"subject":19,"chapter":null,"topic":null},"/api/admin/questionsAvalableForSet/":""}
        $setId = $request->input('setId', null);
        $organization = $request->input('filters.organization', null);
        $subject = $request->input('filters.subject', null);
        $chapter = $request->input('filters.chapter', null);
        $topic = $request->input('filters.topic', null);

        if (!$setId) {
            return apiResponse(false, 'Set ID is required', [], 400);
        }
        $set = QuestionSet::find($setId);
        if (!$set) {
            return apiResponse(false, 'Set not found', [], 404);
        }

        // Fetch all sets with their related data
        // dd($set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? 0);

        $questions = QuestionTbl::with(['meta'])

            ->whereNotIn('question_tbls.id', function ($query) use ($setId) {
                $query->select('question_tbl_id')
                ->from('question_set_question_tbl')
                ->where('question_set_id', $setId);
            })
            // filter part end

            ->join('question_categories as topic', function ($join) {
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
            ->where('subject.id', '=', $set->meta->where('meta_key', 'subject_id')->first()->meta_value ?? 0)
            ->get();

            $ques = $questions->map(function ($question) : object {
                $_newQuestion = new stdClass(); // Initialize an empty standard class object
                $_newQuestion->id = $question->id;
                $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
                $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
                $_newQuestion->subject_name = $question->subject_name;
                $_newQuestion->subject_id = $question->subject_id;
                $_newQuestion->chapter_name = $question->chapter_name;
                $_newQuestion->topic_name = $question->topic_name;
                $_newQuestion->formattedQuestion = (new Question($question))->toObject();

                return $_newQuestion; // Corrected variable name
            });
    
        // Convert the questions to a more structured format
        
        return apiResponse(true, 'Questions fetched successfully', $ques);
    }
    */



    
}
