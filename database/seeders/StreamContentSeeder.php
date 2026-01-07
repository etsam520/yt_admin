<?php

namespace Database\Seeders;

use App\Models\CourseDirectory;
use App\Models\QuestionSet;
use App\Models\StreamContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StreamContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // StreamContent::truncate();

        $courseDirectories = CourseDirectory::all();
        $questionSets = QuestionSet::all();

        if ($courseDirectories->isEmpty()) {
            $this->call(CourseDirectorySeeder::class); // Ensure directories exist
            $courseDirectories = CourseDirectory::all();
        }
        if ($questionSets->isEmpty()) {
            $this->call(QuestionSetSeeder::class); // Ensure question sets exist
            $questionSets = QuestionSet::all();
        }

        $contentTypes = ['video', 'audio', 'question', 'pdf', 'ppt'];

        foreach ($courseDirectories as $directory) {
            foreach ($contentTypes as $type) {
                // For demonstration, not creating actual target_table entries here.
                // In a real app, you'd create video/question/pdf/ppt first, then link.
                // For seeders, we'll assign dummy IDs for now.
                $targetId = rand(1, 100); // Placeholder ID

                StreamContent::create([
                    'type' => $type,
                    'target_table' => Str::singular($type) . '_tbls', // e.g., video_tbls, question_tbls
                    'target_id' => $targetId,
                    'course_directory_id' => $directory->id,
                    'question_sets_id' => ($type === 'question' && $questionSets->isNotEmpty()) ? $questionSets->random()->id : null,
                ]);
            }
        }

        // Also link some question sets directly as stream content (if not already linked)
        foreach ($questionSets as $qSet) {
            if (!StreamContent::where('question_sets_id', $qSet->id)->exists()) {
                StreamContent::create([
                    'type' => 'question', // Or a new type like 'quiz' if you want
                    'target_table' => 'question_sets', // Pointing to the question_sets table
                    'target_id' => $qSet->id,
                    'course_directory_id' => $courseDirectories->random()->id,
                    'question_sets_id' => $qSet->id,
                ]);
            }
        }
    }
}
