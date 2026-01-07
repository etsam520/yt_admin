<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters due to foreign key constraints!

        // Core hierarchical structures first
        $this->call(TradeNodeSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(CourseDirectorySeeder::class);

        // Question sets which depend on courses/trade_nodes
        $this->call(QuestionSetSeeder::class);

        // Stream contents (can link to directories and question sets)
        $this->call(StreamContentSeeder::class);

        // Specific content types that depend on stream_contents
        // Note: These seeders also update stream_contents.target_id
        $this->call(VideoTblSeeder::class);
        $this->call(PptTblSeeder::class);
        $this->call(PdfTblSeeder::class);
        $this->call(QuestionTblSeeder::class); // Important: Must run before QuestionTransSeeder

     

        // Pivot table for many-to-many question sets and questions
        $this->call(QuestionSetQuestionTblSeeder::class);

        // You might have a UserSeeder or other seeders here too
        // $this->call(UserSeeder::class);

        $this->call(AdminUserSeeder::class);
    }
}
