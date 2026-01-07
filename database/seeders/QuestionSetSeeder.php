<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\QuestionSet;
use App\Models\TradeNode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // QuestionSet::truncate();

        $courses = Course::all();
        $tradeNodes = TradeNode::all();

        if ($courses->isEmpty()) {
            $this->call(CourseSeeder::class); // Ensure courses exist
            $courses = Course::all();
        }
        if ($tradeNodes->isEmpty()) {
            $this->call(TradeNodeSeeder::class); // Ensure trade nodes exist
            $tradeNodes = TradeNode::all();
        }

        foreach ($courses as $course) {
            for ($i = 1; $i <= 2; $i++) { // Create 2 question sets per course
                QuestionSet::create([
                    'name' => $course->name . ' Quiz ' . $i,
                    'description' => 'A quiz for ' . $course->name . ' covering key concepts.',
                    'course_id' => $course->id,
                    'trade_node_id' => $course->trade_node_id, // Link to the course's trade node
                ]);
            }
        }

        // Also create some standalone question sets linked only to trade nodes
        foreach ($tradeNodes as $tradeNode) {
            QuestionSet::create([
                'name' => $tradeNode->name . ' Fundamentals Test',
                'description' => 'Test your knowledge on ' . $tradeNode->name . ' fundamentals.',
                'course_id' => null, // Standalone
                'trade_node_id' => $tradeNode->id,
            ]);
        }
    }
}
