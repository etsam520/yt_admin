<?php

namespace Database\Seeders;

use App\Models\QuestionSet;
use App\Models\QuestionTbl;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSetQuestionTblSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_set_question_tbl')->truncate();

        $questionTbls = QuestionTbl::all();
        $questionSets = QuestionSet::all();

        if ($questionTbls->isEmpty()) {
            $this->call(QuestionTblSeeder::class); // Ensure questions exist
            $questionTbls = QuestionTbl::all();
        }
        if ($questionSets->isEmpty()) {
            $this->call(QuestionSetSeeder::class); // Ensure question sets exist
            $questionSets = QuestionSet::all();
        }

        foreach ($questionSets as $questionSet) {
            // Attach random questions to each question set (e.g., 3-5 questions)
            $randomQuestions = $questionTbls->random(rand(3, min(5, $questionTbls->count())));
            foreach ($randomQuestions as $question) {
                DB::table('question_set_question_tbl')->insert([
                    'question_tbl_id' => $question->id,
                    'question_set_id' => $questionSet->id,
                ]);
            }
        }
    }
}
