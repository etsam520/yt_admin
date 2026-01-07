<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\class\Question;
use App\Helpers\QuestionParser;
use App\Helpers\QuestionParserFromText;
use App\Http\Controllers\Controller;
use App\Models\QuestionMeta;
use App\Models\QuestionTbl;
use App\Models\StreamContent;
use App\Models\User;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use stdClass;

class QuestionController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Log::info('Fetching questions with filters', ['request' => $request->all()]);
        $group = $request->input('group', 'pyq'); // default to 'pyq' if not provided
        $qbt = $request->input('qbt','both');
        // here qbt stands for question bank belonging with teacher or admin
        if (preg_match('/^(both)$/i', $qbt))
            $qbt = 0;
        elseif (preg_match('/^(teacher)$/i', $qbt)) 
            $qbt = 1;
        elseif (preg_match('/^(admin)$/i', $qbt)) 
            $qbt = 2; 

        $user = Auth::user();
        $admin = $qbt == 0 ? User::where('role', 'admin')->first() : null;

        $questions = QuestionTbl::with(['meta'])
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
            ->when($qbt == 1 && $user->role !== 'admin', function ($query) use ($user) {
                $query->where('question_tbls.created_by', $user->id);
            })
            ->when($qbt == 2 && $user->role == 'admin', function ($query) use ($user) {
                $query->where('question_tbls.created_by', '=', $user->id);
            })
            ->when($qbt == 0  && $admin != null, function ($query) use ($admin, $user) {
                $query->where(function($q) use ($admin, $user) {
                    $q->whereIn('question_tbls.created_by', [$admin->id, $user->id]);
                });
            })
            ->get();
            $ques = $questions->map(function ($question) : object {
                $_newQuestion = new stdClass(); // Initialize an empty standard class object
                $_newQuestion->id = $question->id;
                $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
                $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
                $_newQuestion->organisation_name = $question->organisation_name?? null;
                $_newQuestion->subject_name = $question->subject_name;
                $_newQuestion->chapter_name = $question->chapter_name;
                $_newQuestion->topic_name = $question->topic_name;
                $_newQuestion->group_name = $question->group_name;
                $_newQuestion->ca_date = $question->ca_date;
                $_newQuestion->formattedQuestion = (new Question($question))->toObject();

                return $_newQuestion; // Corrected variable name
            });
    
        // Convert the questions to a more structured format
        
        return apiResponse(true, 'Questions fetched successfully', $ques);
    }

    /** 
     * Displaying a list of teachers personla questions
     */

    public function questionsByTeacher(Request $request){
        $user = Auth::user();

        $questions = QuestionTbl::with(['meta'])
            ->where('created_by', $user->id)
            ->get();

        $ques = $questions->map(function ($question) : object {
            $_newQuestion = new stdClass(); // Initialize an empty standard class object
            $_newQuestion->id = $question->id;
            $_newQuestion->question_text_en = json_decode($question->question_text_en) ?? $question->question_text_en;
            $_newQuestion->question_text_hi = json_decode($question->question_text_hi) ?? $question->question_text_hi;
            $_newQuestion->formattedQuestion = (new Question($question))->toObject();

            return $_newQuestion; // Corrected variable name
        });

        return apiResponse(true, 'Teacher questions fetched successfully', $ques);
    }

    /*

     $questions = DB::table('question_tbls as q')
            ->join('question_categories as topic', function ($join) {
                $join->on('topic.id', '=', 'q.category_id')
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
                q.*,
                JSON_EXTRACT(q.question, "$.text.en") as question_text_en,
                JSON_EXTRACT(q.question, "$.text.hi") as question_text_hi,
                JSON_EXTRACT(q.options, "$[*].text.en") as options_text_en,
                JSON_EXTRACT(q.options, "$[*].text.hi") as options_text_hi,
                subject.name as subject_name,
                chapter.name as chapter_name,
                topic.name as topic_name
            ')
            ->get();

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        Log::info('Store question request', ['request' => $request->all()]);
        $validator = Validator::make($request->all(), [
            'question.text.en' => 'nullable|string',
            'question.text.hi' => 'nullable|string',
            'question.images' => 'nullable|array',
            // 'question.images.*.path' => 'required_with:question.images|string',
            'question.images.*.serverId' => 'required_with:question.images|integer',

            'type' => 'required|in:multiple_choice',

            'options' => 'required|array|size:4',
            'options.*.text.en' => 'nullable|string',
            'options.*.text.hi' => 'nullable|string',
            'options.*.images' => 'nullable|array',

            'answer' => 'required|string',

            'solution.text.en' => 'nullable|string',
            'solution.text.hi' => 'nullable|string',
            'solution.images' => 'nullable|array',

            'tags' => 'nullable|array',

            'meta.organisationId' => 'nullable|integer',
            // 'meta.subjectId' => 'required|integer',
            // 'meta.chapterId' => 'required|integer',
            // 'meta.topicId' => 'required|integer',
            'meta.subjectId' => ['nullable', 'integer', Rule::requiredIf(request('meta.questionGroup') !== 'ca')],
            'meta.chapterId' => ['nullable', 'integer', Rule::requiredIf(request('meta.questionGroup') !== 'ca')],
            'meta.topicId'   => ['nullable', 'integer', Rule::requiredIf(request('meta.questionGroup') !== 'ca')],
            'meta.questionGroup' => 'nullable|string|in:ca,pyq,npq',
            'ca_date' => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return apiResponse(false, 'Validation failed', $validator->errors());
        }

        $validated = $validator->validated();

        Log::info('Creating question', ['data' => $validated]);

         // Begin transactionS

        try {
            DB::beginTransaction();
            $_q = $validated['question'];
            $_q['images'] = array_map(function ($img) {
                return $img['serverId'];
            }, $_q['images'] ?? []);
            $validated['question'] = $_q;

            $_s = $validated['solution'];
            $_s['images'] = array_map(function ($img) {
                return $img['serverId'];
            }, $_s['images'] ?? []);
            $validated['solution'] = $_s;

            $_opts = $validated['options'];
            $_opts = array_map(function ($opt) {
                $_opt = $opt;
                $_opt['images'] = array_map(function ($img) {
                    return $img['serverId'];
                }, $opt['images'] ?? []);
                return $_opt;
            }, $_opts);
            $validated['options'] = $_opts;
            // Create question

            $question = QuestionTbl::create([
                'question' => $validated['question'],
                'type' => $validated['type'] ?? 'multiple_choice',
                'options' => $validated['options'],
                'answer' => $validated['answer'],
                'solution' => $validated['solution'],
                'is_public' => $validated['is_public'] ?? false,
                'category_id' => $validated['meta']['topicId'] ?? null,
                'created_by' => auth()->user()->id,
                'category_depth_index' => $validated['meta']['topicId'] ? 'topic' : null, // assumed fixed depth name
            ]);

            $meta = [
                ['question_id' => $question->id, 'meta_key' => 'subject_id', 'meta_value' => $validated['meta']['subjectId']],
                ['question_id' => $question->id, 'meta_key' => 'chapter_id', 'meta_value' => $validated['meta']['chapterId']],
                ['question_id' => $question->id, 'meta_key' => 'topic_id', 'meta_value' => $validated['meta']['topicId']],
                ['question_id' => $question->id, 'meta_key' => 'tags', 'meta_value' => json_encode($validated['tags'] ?? [])],
                ['question_id' => $question->id, 'meta_key' => 'group', 'meta_value' => $validated['meta']['questionGroup'] ?? 'pyq'],
                ['question_id' => $question->id, 'meta_key' => 'ca_date', 'meta_value' => $validated['meta']['ca_date'] ?? null],
            ];

            QuestionMeta::insert($meta = array_filter($meta, function ($m) {
                return $m['meta_value'] !== null;
            }));

            DB::commit();

            return apiResponse(true, 'Question created successfully', $question, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create question', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return apiResponse(false, 'Failed to create question', ['error' => $e->getMessage()], 500);
        }
    }

    public function __store(Request $request)
    {
    //    return response()->json(['message' => 'This endpoint is not implemented yet.'], 200);
        $validated = $request->validate([
            'question.text' => 'required',
            'question.images' => 'array',
            'options' => 'required|array|size:4',
            'options.*.text' => 'required',
            'options.*.images' => 'array',
            'answer.text' => 'required',
            'answer.images' => 'array',
            'solution.text' => 'nullable',
            'solution.images' => 'array',
            'tags' => 'nullable|array '
        ]);

        // Handle file uploads and store paths in the arrays
        $validated['question']['images'] = $this->uploadImages($request->file('question.images'));
        
        foreach ($validated['options'] as $i => $option) {
            $validated['options'][$i]['images'] = $this->uploadImages($request->file("options.$i.images"));
        }
        
        $validated['answer']['images'] = $this->uploadImages($request->file('answer.images'));
        $validated['solution']['images'] = $this->uploadImages($request->file('solution.images'));
        $validated['created_by'] = auth()->user()->id;

        $question = QuestionTbl::create($validated);
        
        return response()->json($question, 201);
    }

    protected function uploadImages($files)
    {
        if (!$files) return [];
        
        $paths = [];
        foreach ($files as $file) {
            $path = $file->store('media');
            $paths[] = './' . $path;
        }
        return $paths;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'meta' => 'nullable|array',
            'question' => 'required|array',
            'type' => 'nullable|string',
            'options' => 'required|array',
            'answer' => 'required|string',
            'solution' => 'required|array',
            'tags' => 'nullable|array',
        ]);

        if(isset($validated['meta'])) unset($validated['meta']);
        $question = QuestionTbl::findOrFail($id);
        $question->update([
            'question' => $validated['question'],
            'type' => $validated['type'] ?? 'multiple_choice',
            'options' => $validated['options'],
            'answer' => $validated['answer'],
            'solution' => $validated['solution'],
        ]);

        $meta = [
            // ['question_id' => $question->id, 'meta_key' => 'subject_id', 'meta_value' => $validated['meta']['subjectId']],
            // ['question_id' => $question->id, 'meta_key' => 'chapter_id', 'meta_value' => $validated['meta']['chapterId']],
            // ['question_id' => $question->id, 'meta_key' => 'topic_id', 'meta_value' => $validated['meta']['topicId']],
            ['question_id' => $question->id, 'meta_key' => 'tags', 'meta_value' => $validated['tags'] ?? null],
        ];
        QuestionMeta::upsert($meta, ['question_id', 'meta_key'], ['meta_value']);

        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = QuestionTbl::findOrFail($id);
        $question->delete();

        return response()->json([
            'message' => 'Question deleted successfully'
        ], 200);
    }

    /**
     * Get the question by ID.
     */


    public function bulkImport(Request $request)
    {
        try {
            Log::info('Bulk import request received', ['request' => $request->all()]);
            $user = auth()->user();
            $file = $request->file('file');
            $payload = $request->all();

            // if (!$file) {
            //     return response()->json(['error' => 'No file provided'], 400);
            // }

            if ((empty($payload['subjectId']) || $payload['subjectId'] == "null" || $payload['subjectId'] == null || is_null($payload['subjectId']) || $payload['subjectId'] == '') && $payload['questionGroup'] !== 'ca') {
                return response()->json(['error' => 'Subject ID is required'], 400);
            }
            if ((empty($payload['chapterId']) || $payload['chapterId'] == "null" || $payload['chapterId'] == null || is_null($payload['chapterId']) || $payload['chapterId'] == '') && $payload['questionGroup'] !== 'ca') {
                return response()->json(['error' => 'Chapter ID is required'], 400);
            }
            if ((empty($payload['topicId']) || $payload['topicId'] == "null" || $payload['topicId'] == null || is_null($payload['topicId']) || $payload['topicId'] == '') && $payload['questionGroup'] !== 'ca') {
                return response()->json(['error' => 'Topic ID is required'], 400);
            }

            // ✅ Create a log file
            $logFile = storage_path('logs/pandoc.log');

            // Save uploaded file temporarily
            $dirPrefix = 'word_files/' . date('Y_m_d_h_i') . "-" . rand(1000, 9999);
            $inputPath  = public_path($dirPrefix . '/' . uniqid() . '_' . $file->getClientOriginalName());
            $outputPath = public_path($dirPrefix . '/output.txt');
            $mediaPath  = public_path($dirPrefix . '/');

            $file->move(public_path($dirPrefix . '/'), basename($inputPath));

            $pandocPath = env('PANDOC_PATH', 'pandoc');
            $command = sprintf(
                '%s %s -t plain --wrap=none -o %s --extract-media=%s --lua-filter=%s --quiet 2>&1',
                escapeshellarg($pandocPath),
                escapeshellarg($inputPath),
                escapeshellarg($outputPath),
                escapeshellarg($mediaPath),
                escapeshellarg(base_path("resources/pandoc/image_math.lua"))
            );

            // $command = sprintf(
            //     'pandoc %s -t plain --columns=120 --wrap=none  -o %s --extract-media=%s --lua-filter=%s --quiet 2>&1',
            //     escapeshellarg($inputPath),
            //     escapeshellarg($outputPath),
            //     escapeshellarg($mediaPath),
            //     escapeshellarg(base_path("resources/pandoc/image_math.lua"))
            // );
            
            // $command = sprintf(
            //     'pandoc %s -t markdown -o %s --extract-media=%s 2>&1',
            //     escapeshellarg($inputPath),
            //     escapeshellarg($outputPath),
            //     escapeshellarg($mediaPath)
            // );

            //  $command = sprintf(
            //     'pandoc %s -f docx -t rtf -o %s --extract-media=%s 2>&1',
            //     escapeshellarg($inputPath),
            //     escapeshellarg($outputPath),
            //     escapeshellarg($mediaPath)
            // );

            // ✅ Execute pandoc
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            // ✅ Write output to log file
            file_put_contents($logFile, "---- " . now() . " ----\n", FILE_APPEND);
            file_put_contents($logFile, implode("\n", $output) . "\n\n", FILE_APPEND);

            if ($returnVar !== 0) {
                return response()->json([
                    'error' => 'Pandoc conversion failed',
                    'command' => $command,
                    'log_file' => $logFile
                ], 500);
            }

            // ✅ Read the converted text file
            $convertedText = file_exists($outputPath) ? file_get_contents($outputPath) : null;

            $logFIle = storage_path('logs/block_output.log');

            file_put_contents($logFIle, "row text Block: \n" . $convertedText . "\n\n", FILE_APPEND);

            // Log::info('Parsed questions', $convertedText);

            $perser = new QuestionParser();
            $questions = $perser->parse($convertedText);
            // Log::info('Parsed questions', $questions);

            if (!is_array($questions)) {
                return response()->json([
                    'error' => 'Failed to parse questions from the document.',
                    'details' => $questions
                ], 500);
            }

            // Wrap DB operations in a transaction
            DB::beginTransaction();
            $saved = $this->saveBulkQuestions(questions: $questions, mediaPath: $mediaPath, payload: $payload , user: $user);
            if (!$saved) {
                DB::rollBack();
                return response()->json(['error' => 'No questions were imported'], 400);
            }
            DB::commit();

            return response()->json([
                'message' => 'Bulk import successful',
            ], 200);
        } catch (\Throwable $e) {
            // ensure any open transaction is rolled back
            try {
                DB::rollBack();
            } catch (\Throwable $inner) {
                // ignore
            }

            if (config('app.debug')) {
                Log::error('Bulk import failed', [
                    'message' => $e->getMessage(),
                    'FILE' =>$e->getFile(), 
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            return response()->json([
                'error' => 'Bulk import failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function saveBulkQuestions(array $questions, string $mediaPath , array $payload , User $user) : bool
    {
      
        
        $prettyFormate = $this->questionsPrettyFormat($questions, $mediaPath);

        if(count($prettyFormate) == 0){
            return false;
        }

        foreach ($prettyFormate as $q) {
            $q['chapter_id'] = $payload['chapterId'];
            $q['topic_id'] = $payload['topicId'];
            $q['created_by'] = $user->id;
            $q['updated_by'] = $user->id;


            logger()->info('Saving Question', ['q' => $q]);
            

            $topicId = (empty($payload['topicId']) || $payload['topicId'] == "null" || $payload['topicId'] == null || is_null($payload['topicId']) || $payload['topicId'] == '') ? null : $payload['topicId'];

           $created_question = QuestionTbl::create([
                'question' => $q['question'] ?? null,
                'type' => $q['type'] ?? 'multiple_choice',
                'options' => $q['options'] ?? null,
                'answer' => $q['answer'] ?? null,
                'solution' => $q['solution'] ?? null,
                'is_public' => $q['is_public'] ?? false,
                'category_id' => $topicId,
                'created_by' => $q['created_by'] ?? auth()->user()->id,
                'category_depth_index' => $topicId ? 'topic' : null, // assumed fixed depth name
            ]);
            $meta = [
                ['question_id' => $created_question->id, 'meta_key' => 'organisation_id', 'meta_value' => $payload['organisationId'] ?? null],
                ['question_id' => $created_question->id, 'meta_key' => 'subject_id', 'meta_value' => $payload['subjectId']],
                ['question_id' => $created_question->id, 'meta_key' => 'chapter_id', 'meta_value' => $payload['chapterId']],
                ['question_id' => $created_question->id, 'meta_key' => 'topic_id', 'meta_value' => $payload['topicId']],
                ['question_id' => $created_question->id, 'meta_key' => 'tags', 'meta_value' => json_encode($q['tags'] ?? [])],
                ['question_id' => $created_question->id, 'meta_key' => 'group', 'meta_value' => $payload['questionGroup'] ?? 'pyq'],
                ['question_id' => $created_question->id, 'meta_key' => 'ca_date', 'meta_value' => $payload['ca_date'] ?? null],
            ];

            QuestionMeta::insert($meta = array_filter($meta, function ($m) {
                return !(empty($m['meta_value']) || $m['meta_value'] == "null" || $m['meta_value'] == null || is_null($m['meta_value']) || $m['meta_value'] == '');
            }));
        }
        return true;
    }

    public function questionsPrettyFormat(array $questions, string $mediaPath) : array
    {
        $bloc = storage_path("logs/block_output.log");
        // file_put_contents($bloc, print_r($questions, true));
        $formated_questions = array_map(function ($q) use($mediaPath) {
            if(($q['key'] == null || $q['key'] == '') 
                && ($q['question']['text']['en'] == null || $q['question']['text']['en'] == '') || ($q['question']['text']['hi'] == null || $q['question']['text']['hi'] == '')) {
                return null;
            }
            // Log::info('question', ['q' => $q['question']['images'] ?? []]);
            // return [];
            $_qi = array_map(function ($img) use($mediaPath) {
                logger()->info('Uploading Question image', ['image' => $img, 'mediaPath' => $mediaPath]); 
                return self::uploadQuestionMedia($mediaPath, $img)->id;
            }, $q['question']['images'] ?? []);

            $_qt = $q['question']['text'] ?? null;

            $_qo  = (function () use ($q, $mediaPath) {
                // dd($q['options']);
                $hindiOptions = array_filter($q['options'] ?? [], function ($opt) {
                    return isset($opt['lang']) && $opt['lang'] === 'hi';
                });
                $hindiOptions = array_values($hindiOptions);
                $englishOptions = array_filter($q['options'] ?? [], function ($opt) {
                    return isset($opt['lang']) && $opt['lang'] === 'en';
                });
                // dd($hindiOptions, $englishOptions);
                $englishOptions = array_values($englishOptions);
                $maxOptions = max(count($hindiOptions), count($englishOptions));
                $mergedOptions = [];
                for ($i = 0; $i < $maxOptions; $i++) {
                    // merge and deduplicate image names
                    $mergedImages = array_unique(
                        array_merge(
                            $hindiOptions[$i]['images'] ?? [],
                            $englishOptions[$i]['images'] ?? []
                        ),
                        SORT_STRING | SORT_FLAG_CASE
                    );

                    $mergedOptions[] = [
                        'text' => [
                            'en' => $englishOptions[$i]['text'] ?? null,
                            'hi' => $hindiOptions[$i]['text'] ?? null,
                        ],
                        'images' => array_map(function ($img) use($mediaPath) {
                            $file = self::uploadQuestionMedia($mediaPath, $img);
                            return $file ? $file->id : null;
                        }, $mergedImages),
                    ];
                }
                return $mergedOptions;
            })();

            $_qa = $q['answer'] ;
            $_qst = $q['solution']['text'] ?? null;
            $_qsi = array_map(function ($img) use($mediaPath) {
                return self::uploadQuestionMedia($mediaPath, $img)->id;
            }, $question['solution']['images'] ?? []); 

            $_qspliz = $q['specialization'] ?? [];

            return [
                'question' => [
                    'text' => $_qt,
                    'images' => $_qi,
                ],
                'type' => $q['type'] ?? 'multiple_choice',
                'options' => $_qo,
                'answer' => $_qa,
                'solution' => [
                    'text' => $_qst,
                    'images' => $_qsi,
                ],
                'specialization' => $_qspliz,
            ];
        },$questions); 
        // if (is_dir($mediaPath)) {
        //     File::deleteDirectory($mediaPath); // Laravel helper
        //     exec("rm -rf " . escapeshellarg($mediaPath));
        // }
        return array_filter($formated_questions);
    }

    private static function uploadQuestionMedia($path, $endPoint = "media/image1.jpeg") : \App\Models\UploadedFile|null
    {
        try {
            $filePath = $path . '/' . $endPoint;

            if (!file_exists($filePath)) {
                return null;
            }

            // Extract original filename from endpoint using regex
            $pattern = '/^.*\/([^\/\s"]+\.(?:jpg|jpeg|png|gif|bmp|webp))$/i';
            $originalName = preg_filter($pattern, '$1', $endPoint);

            // Get file size and mime
            $fileSize  = filesize($filePath);
            $mimeType  = mime_content_type($filePath);

            // Generate unique filename
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueFilename = time() . rand(1000, 9999) . '.' . $ext;

            // Save file into public/uploads/all
            $destinationPath = public_path('uploads/all/' . $uniqueFilename);
            copy($filePath, $destinationPath);

            // Save info to database
            $uploadedFile = \App\Models\UploadedFile::create([
                'original_name' => $originalName,
                'stored_name'   => $uniqueFilename,
                'path'          => 'uploads/all/' . $uniqueFilename,
                'mime_type'     => $mimeType,
                'size'          => $fileSize,
            ]);

            return $uploadedFile;

        } catch (\Throwable $th) {
            Log::error("FILE_UPLOAD_ERROR: " . $th->getMessage());
            return  null;
        }
    }

    
    
}

