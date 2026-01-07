<?php

namespace Database\Seeders;

use App\Models\QuestionTbl;
use App\Models\StreamContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTblSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // return;
        // QuestionTbl::truncate();

        $questionStreamContents = StreamContent::where('type', 'question')->get();

        if ($questionStreamContents->isEmpty()) {
            $this->call(StreamContentSeeder::class);
            $questionStreamContents = StreamContent::where('type', 'question')->get();
        }



        $questions = [
            [
                "question" => [
                    "text" => "ffhfhf",
                    "images" => [
                        [
                            "path" => "uploads/all/1751003202.png",
                            "serverId" => 27
                        ]
                    ]
                ],
                "type" => "multiple_choice",
                "options" => [
                    [
                        "text" => "",
                        "images" => [
                            [
                                "path" => "uploads/all/1751003210.png",
                                "serverId" => 28
                            ]
                        ]
                    ],
                    [
                        "text" => "Option 2 text",
                        "images" => []
                    ],
                    [
                        "text" => "Option 3 text",
                        "images" => []
                    ],
                    [
                        "text" => "Option 4 text",
                        "images" => []
                    ]
                ],
                "answer" => [
                    "text" => "1",
                    "images" => []
                ],
                "solution" => [
                    "text" => "The correct answer is Option 1",
                    "images" => []
                ],
                "positive_marks" => 4,
                "negative_marks" => 1
            ],
            [
                "question" => [
                    "text" => "What is the capital of France?",
                    "images" => []
                ],
                "type" => "multiple_choice",
                "options" => [
                    [
                        "text" => "Berlin",
                        "images" => []
                    ],
                    [
                        "text" => "Madrid",
                        "images" => []
                    ],
                    [
                        "text" => "Paris",
                        "images" => []
                    ],
                    [
                        "text" => "Rome",
                        "images" => []
                    ]
                ],
                "answer" => [
                    "text" => "3",
                    "images" => []
                ],
                "solution" => [
                    "text" => "Paris is the capital of France.",
                    "images" => []
                ],
                "positive_marks" => 4,
                "negative_marks" => 1
            ]
        ];


        foreach ($questionStreamContents as $streamContent) {
            $q = $questions[array_rand($questions)];
            $data = [
                'question' => json_encode($q['question']),
                'type' => $q['type'],
                'options' => json_encode($q['options']),
                'answer' => json_encode($q['answer']),
                'solution' => json_encode($q['solution']),
                'positive_marks' => $q['positive_marks'],
                'negative_marks' => $q['negative_marks'],
                'is_public' => rand(0, 1),
                'stream_content_id' => $streamContent->id,
                'created_by' => 1, // Assuming admin user
            ];
            QuestionTbl::create($data);
          
            $streamContent->update([
                'target_table' => 'question_tbls',
                'target_id' => QuestionTbl::latest()->first()->id,
            ]);
        }
    }
}
